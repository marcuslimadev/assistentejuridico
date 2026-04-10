<?php

namespace App\Services;

use App\Models\CreditPurchase;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class MercadoPagoService
{
    public function isConfigured(): bool
    {
        return filled(config('services.mercado_pago.access_token'));
    }

    public function createPixPurchase(User $user, int $creditsQuantity): CreditPurchase
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('Mercado Pago não configurado no servidor.');
        }

        if ($creditsQuantity < 1) {
            throw new RuntimeException('A quantidade de créditos deve ser maior que zero.');
        }

        $unitPriceCents = (int) config('services.billing.consulta_unit_price_cents', 5);
        $totalAmountCents = $creditsQuantity * $unitPriceCents;
        $externalReference = (string) Str::uuid();

        $response = Http::timeout(90)
            ->withToken(config('services.mercado_pago.access_token'))
            ->withHeaders([
                'X-Idempotency-Key' => $externalReference,
            ])
            ->post('https://api.mercadopago.com/v1/payments', [
                'transaction_amount' => round($totalAmountCents / 100, 2),
                'description' => sprintf('Compra de %d crédito(s) de consulta', $creditsQuantity),
                'payment_method_id' => 'pix',
                'notification_url' => config('services.mercado_pago.notification_url'),
                'external_reference' => $externalReference,
                'payer' => [
                    'email' => $user->email,
                    'first_name' => $user->name,
                ],
            ]);

        if (! $response->successful()) {
            throw new RuntimeException('Falha ao criar cobrança Pix no Mercado Pago.');
        }

        $payload = $response->json();

        return CreditPurchase::create([
            'user_id' => $user->id,
            'external_reference' => $externalReference,
            'mercado_pago_payment_id' => (string) $payload['id'],
            'status' => $payload['status'] ?? 'pending',
            'credits_quantity' => $creditsQuantity,
            'unit_price_cents' => $unitPriceCents,
            'total_amount_cents' => $totalAmountCents,
            'payer_email' => $user->email,
            'pix_qr_code' => data_get($payload, 'point_of_interaction.transaction_data.qr_code'),
            'pix_qr_code_base64' => data_get($payload, 'point_of_interaction.transaction_data.qr_code_base64'),
            'ticket_url' => data_get($payload, 'point_of_interaction.transaction_data.ticket_url'),
            'payment_payload' => $payload,
            'expires_at' => data_get($payload, 'date_of_expiration') ? Carbon::parse(data_get($payload, 'date_of_expiration')) : null,
            'approved_at' => ($payload['status'] ?? null) === 'approved' ? now() : null,
        ]);
    }

    public function syncPurchase(CreditPurchase $purchase): CreditPurchase
    {
        if (! $this->isConfigured()) {
            return $purchase;
        }

        if (! $purchase->mercado_pago_payment_id) {
            return $purchase;
        }

        $response = Http::timeout(90)
            ->withToken(config('services.mercado_pago.access_token'))
            ->get('https://api.mercadopago.com/v1/payments/'.$purchase->mercado_pago_payment_id);

        if (! $response->successful()) {
            throw new RuntimeException('Falha ao consultar pagamento no Mercado Pago.');
        }

        return $this->updatePurchaseFromPayload($purchase, $response->json());
    }

    public function syncPurchaseByPaymentId(string $paymentId): ?CreditPurchase
    {
        if (! $this->isConfigured()) {
            return null;
        }

        $response = Http::timeout(90)
            ->withToken(config('services.mercado_pago.access_token'))
            ->get('https://api.mercadopago.com/v1/payments/'.$paymentId);

        if (! $response->successful()) {
            throw new RuntimeException('Falha ao consultar webhook do Mercado Pago.');
        }

        $payload = $response->json();

        $purchase = CreditPurchase::query()
            ->where('mercado_pago_payment_id', (string) ($payload['id'] ?? $paymentId))
            ->orWhere('external_reference', $payload['external_reference'] ?? '')
            ->first();

        if (! $purchase) {
            return null;
        }

        return $this->updatePurchaseFromPayload($purchase, $payload);
    }

    protected function updatePurchaseFromPayload(CreditPurchase $purchase, array $payload): CreditPurchase
    {
        $purchase->forceFill([
            'mercado_pago_payment_id' => (string) ($payload['id'] ?? $purchase->mercado_pago_payment_id),
            'status' => $payload['status'] ?? $purchase->status,
            'pix_qr_code' => data_get($payload, 'point_of_interaction.transaction_data.qr_code', $purchase->pix_qr_code),
            'pix_qr_code_base64' => data_get($payload, 'point_of_interaction.transaction_data.qr_code_base64', $purchase->pix_qr_code_base64),
            'ticket_url' => data_get($payload, 'point_of_interaction.transaction_data.ticket_url', $purchase->ticket_url),
            'payment_payload' => $payload,
            'expires_at' => data_get($payload, 'date_of_expiration') ? Carbon::parse(data_get($payload, 'date_of_expiration')) : $purchase->expires_at,
            'approved_at' => ($payload['status'] ?? null) === 'approved' ? ($purchase->approved_at ?? now()) : $purchase->approved_at,
        ])->save();

        return $purchase->fresh();
    }
}