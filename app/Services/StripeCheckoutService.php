<?php

namespace App\Services;

use App\Models\CreditPurchase;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class StripeCheckoutService
{
    public function isConfigured(): bool
    {
        return filled(config('services.stripe.secret_key'));
    }

    public function createPixPurchase(User $user, int $creditsQuantity): CreditPurchase
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('Stripe não configurada no servidor.');
        }

        if ($creditsQuantity < 1) {
            throw new RuntimeException('A quantidade de créditos deve ser maior que zero.');
        }

        $unitPriceCents = (int) config('services.billing.consulta_unit_price_cents', 5);
        $externalReference = (string) Str::uuid();

        $response = $this->stripeRequest()
            ->asForm()
            ->post('https://api.stripe.com/v1/checkout/sessions', [
                'mode' => 'payment',
                'success_url' => config('services.stripe.success_url'),
                'cancel_url' => config('services.stripe.cancel_url'),
                'customer_email' => $user->email,
                'locale' => 'pt-BR',
                'payment_method_types' => ['pix'],
                'payment_method_options' => [
                    'pix' => [
                        'expires_after_seconds' => 14400,
                    ],
                ],
                'line_items' => [[
                    'quantity' => $creditsQuantity,
                    'price_data' => [
                        'currency' => 'brl',
                        'unit_amount' => $unitPriceCents,
                        'product_data' => [
                            'name' => 'Crédito de consulta DataJud',
                            'description' => '1 crédito libera 1 consulta DataJud.',
                        ],
                    ],
                ]],
                'metadata' => [
                    'external_reference' => $externalReference,
                    'user_id' => (string) $user->id,
                    'credits_quantity' => (string) $creditsQuantity,
                ],
                'payment_intent_data' => [
                    'description' => sprintf('Compra de %d crédito(s) de consulta', $creditsQuantity),
                    'metadata' => [
                        'external_reference' => $externalReference,
                        'user_id' => (string) $user->id,
                        'credits_quantity' => (string) $creditsQuantity,
                    ],
                    'receipt_email' => $user->email,
                ],
            ]);

        if (! $response->successful()) {
            throw new RuntimeException('Falha ao criar cobrança Pix na Stripe.');
        }

        $payload = $response->json();

        return CreditPurchase::create([
            'user_id' => $user->id,
            'payment_provider' => 'stripe',
            'external_reference' => $externalReference,
            'provider_session_id' => (string) ($payload['id'] ?? ''),
            'provider_payment_id' => is_string($payload['payment_intent'] ?? null) ? $payload['payment_intent'] : null,
            'status' => $this->mapCheckoutStatus($payload['status'] ?? null, $payload['payment_status'] ?? null),
            'credits_quantity' => $creditsQuantity,
            'unit_price_cents' => $unitPriceCents,
            'total_amount_cents' => $creditsQuantity * $unitPriceCents,
            'payer_email' => $user->email,
            'ticket_url' => $payload['url'] ?? null,
            'payment_payload' => $payload,
            'expires_at' => isset($payload['expires_at']) ? Carbon::createFromTimestamp((int) $payload['expires_at']) : null,
            'approved_at' => ($payload['payment_status'] ?? null) === 'paid' ? now() : null,
        ]);
    }

    public function validateWebhookSignature(Request $request): bool
    {
        $secret = (string) config('services.stripe.webhook_secret', '');

        if ($secret === '') {
            return true;
        }

        $signatureHeader = (string) $request->header('Stripe-Signature', '');

        if ($signatureHeader === '') {
            return false;
        }

        $signatureParts = $this->parseStripeSignatureHeader($signatureHeader);
        $timestamp = $signatureParts['t'][0] ?? null;
        $signatures = $signatureParts['v1'] ?? [];

        if (! $timestamp || $signatures === []) {
            return false;
        }

        if (abs(time() - (int) $timestamp) > 300) {
            return false;
        }

        $payload = $request->getContent();
        $expectedSignature = hash_hmac('sha256', $timestamp.'.'.$payload, $secret);

        foreach ($signatures as $signature) {
            if (hash_equals($expectedSignature, $signature)) {
                return true;
            }
        }

        return false;
    }

    public function parseWebhookEvent(Request $request): ?array
    {
        $payload = json_decode($request->getContent(), true);

        return is_array($payload) ? $payload : null;
    }

    public function syncPurchase(CreditPurchase $purchase): CreditPurchase
    {
        if (! $this->isConfigured() || ! $purchase->provider_session_id) {
            return $purchase;
        }

        $response = $this->stripeRequest()->get(
            'https://api.stripe.com/v1/checkout/sessions/'.$purchase->provider_session_id,
            ['expand' => ['payment_intent']]
        );

        if (! $response->successful()) {
            throw new RuntimeException('Falha ao consultar pagamento na Stripe.');
        }

        return $this->updatePurchaseFromSession($purchase, $response->json());
    }

    public function syncPurchaseFromSessionData(array $session, ?string $overrideStatus = null): ?CreditPurchase
    {
        $purchase = CreditPurchase::query()
            ->where('provider_session_id', (string) ($session['id'] ?? ''))
            ->orWhere('external_reference', data_get($session, 'metadata.external_reference', ''))
            ->first();

        if (! $purchase) {
            return null;
        }

        return $this->updatePurchaseFromSession($purchase, $session, $overrideStatus);
    }

    protected function updatePurchaseFromSession(CreditPurchase $purchase, array $session, ?string $overrideStatus = null): CreditPurchase
    {
        $paymentIntent = $session['payment_intent'] ?? null;
        $paymentIntentId = is_array($paymentIntent)
            ? ($paymentIntent['id'] ?? $purchase->provider_payment_id)
            : ($paymentIntent ?: $purchase->provider_payment_id);

        $hostedInstructionsUrl = is_array($paymentIntent)
            ? data_get($paymentIntent, 'next_action.pix_display_qr_code.hosted_instructions_url')
            : null;

        $purchase->forceFill([
            'payment_provider' => 'stripe',
            'provider_session_id' => (string) ($session['id'] ?? $purchase->provider_session_id),
            'provider_payment_id' => $paymentIntentId,
            'status' => $this->mapCheckoutStatus($session['status'] ?? null, $session['payment_status'] ?? null, $overrideStatus),
            'ticket_url' => $hostedInstructionsUrl ?: ($session['url'] ?? $purchase->ticket_url),
            'payment_payload' => $session,
            'expires_at' => isset($session['expires_at']) ? Carbon::createFromTimestamp((int) $session['expires_at']) : $purchase->expires_at,
            'approved_at' => ($this->mapCheckoutStatus($session['status'] ?? null, $session['payment_status'] ?? null, $overrideStatus) === 'approved') ? ($purchase->approved_at ?? now()) : $purchase->approved_at,
        ])->save();

        return $purchase->fresh();
    }

    protected function stripeRequest()
    {
        return Http::timeout(90)
            ->acceptJson()
            ->withToken((string) config('services.stripe.secret_key'));
    }

    protected function mapCheckoutStatus(?string $sessionStatus, ?string $paymentStatus, ?string $overrideStatus = null): string
    {
        return match (true) {
            $overrideStatus === 'approved' => 'approved',
            $overrideStatus === 'failed' => 'failed',
            $overrideStatus === 'expired' => 'expired',
            $paymentStatus === 'paid' => 'approved',
            $sessionStatus === 'expired' => 'expired',
            default => 'pending',
        };
    }

    protected function parseStripeSignatureHeader(string $signatureHeader): array
    {
        $parts = [];

        foreach (explode(',', $signatureHeader) as $part) {
            [$key, $value] = array_pad(explode('=', trim($part), 2), 2, null);

            if ($key === null || $value === null) {
                continue;
            }

            $parts[$key] ??= [];
            $parts[$key][] = $value;
        }

        return $parts;
    }
}