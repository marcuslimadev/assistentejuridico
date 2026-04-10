<?php

namespace App\Services;

use App\Models\CreditPurchase;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;

class StripeCheckoutService
{
    protected const SUPPORTED_PAYMENT_METHODS = ['card', 'boleto'];
    protected const BOLETO_MINIMUM_AMOUNT_CENTS = 500;

    public function isConfigured(): bool
    {
        return filled(config('services.stripe.secret_key'));
    }

    public function createCheckoutPurchase(User $user, int $creditsQuantity, string $paymentMethod): CreditPurchase
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('Stripe não configurada no servidor.');
        }

        if ($creditsQuantity < 1) {
            throw new RuntimeException('A quantidade de créditos deve ser maior que zero.');
        }

        if (! in_array($paymentMethod, self::SUPPORTED_PAYMENT_METHODS, true)) {
            throw new RuntimeException('Forma de pagamento invalida.');
        }

        $unitPriceCents = (int) config('services.billing.consulta_unit_price_cents', 5);
        $totalAmountCents = $creditsQuantity * $unitPriceCents;
        $externalReference = (string) Str::uuid();

        if ($paymentMethod === 'boleto' && $totalAmountCents < self::BOLETO_MINIMUM_AMOUNT_CENTS) {
            throw new RuntimeException('Boleto disponivel apenas para compras a partir de R$ 5,00. Aumente a quantidade de creditos e tente novamente.');
        }

        $payload = [
            'mode' => 'payment',
            'success_url' => config('services.stripe.success_url'),
            'cancel_url' => config('services.stripe.cancel_url'),
            'customer_email' => $user->email,
            'locale' => 'pt-BR',
            'payment_method_types' => [$paymentMethod],
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
                'payment_method' => $paymentMethod,
            ],
            'payment_intent_data' => [
                'description' => sprintf('Compra de %d crédito(s) de consulta', $creditsQuantity),
                'metadata' => [
                    'external_reference' => $externalReference,
                    'user_id' => (string) $user->id,
                    'credits_quantity' => (string) $creditsQuantity,
                    'payment_method' => $paymentMethod,
                ],
                'receipt_email' => $user->email,
            ],
        ];

        if ($paymentMethod === 'boleto') {
            $payload['tax_id_collection'] = [
                'enabled' => 'true',
            ];
            $payload['payment_method_options'] = [
                'boleto' => [
                    'expires_after_days' => 3,
                ],
            ];
        }

        $response = $this->stripeRequest()
            ->asForm()
            ->post('https://api.stripe.com/v1/checkout/sessions', $payload);

        if (! $response->successful()) {
            throw new RuntimeException($this->buildStripeErrorMessage(
                $response->status(),
                $response->json(),
                'Falha ao criar checkout na Stripe.',
                [
                    'user_id' => $user->id,
                    'credits_quantity' => $creditsQuantity,
                    'payment_method' => $paymentMethod,
                ]
            ));
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
            'total_amount_cents' => $totalAmountCents,
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
            throw new RuntimeException($this->buildStripeErrorMessage(
                $response->status(),
                $response->json(),
                'Falha ao consultar pagamento na Stripe.',
                [
                    'purchase_id' => $purchase->id,
                    'provider_session_id' => $purchase->provider_session_id,
                ]
            ));
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

        $hostedVoucherUrl = is_array($paymentIntent)
            ? data_get($paymentIntent, 'next_action.boleto_display_details.hosted_voucher_url')
            : null;

        $purchase->forceFill([
            'payment_provider' => 'stripe',
            'provider_session_id' => (string) ($session['id'] ?? $purchase->provider_session_id),
            'provider_payment_id' => $paymentIntentId,
            'status' => $this->mapCheckoutStatus($session['status'] ?? null, $session['payment_status'] ?? null, $overrideStatus),
            'ticket_url' => $hostedVoucherUrl ?: ($hostedInstructionsUrl ?: ($session['url'] ?? $purchase->ticket_url)),
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

    protected function buildStripeErrorMessage(int $status, ?array $payload, string $fallbackMessage, array $context = []): string
    {
        $errorMessage = trim((string) data_get($payload, 'error.message', ''));
        $errorParam = trim((string) data_get($payload, 'error.param', ''));
        $requestLogUrl = trim((string) data_get($payload, 'error.request_log_url', ''));

        Log::warning('Stripe request failed.', array_filter([
            'status' => $status,
            'error_message' => $errorMessage,
            'error_param' => $errorParam,
            'request_log_url' => $requestLogUrl,
            'payload' => $payload,
            ...$context,
        ], static fn ($value) => $value !== null && $value !== ''));

        if (preg_match('/payment method type provided:\s*([a-z_]+)/i', $errorMessage, $matches) === 1) {
            $invalidMethod = strtolower($matches[1]);

            return match ($invalidMethod) {
                'pix' => 'A conta Stripe ainda nao esta habilitada para Pix. Ative o Pix no painel da Stripe e tente novamente.',
                'boleto' => 'O boleto ainda nao esta habilitado na conta Stripe. Ative o boleto no painel da Stripe e tente novamente.',
                'card' => 'O pagamento por cartao nao esta habilitado na conta Stripe. Verifique os metodos de pagamento no painel.',
                default => 'A forma de pagamento escolhida nao esta habilitada na conta Stripe.',
            };
        }

        if ($errorMessage !== '') {
            return $fallbackMessage.' '.$errorMessage;
        }

        return $fallbackMessage.' Codigo HTTP '.$status.'.';
    }
}