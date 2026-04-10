<?php

namespace App\Http\Controllers;

use App\Models\CreditPurchase;
use App\Services\ConsultaCreditService;
use App\Services\StripeCheckoutService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;

class CreditController extends Controller
{
    public function index(Request $request)
    {
        $activePurchase = CreditPurchase::query()
            ->where('user_id', $request->user()->id)
            ->where('payment_provider', 'stripe')
            ->where('status', 'pending')
            ->latest()
            ->first();

        $recentPurchases = CreditPurchase::query()
            ->where('user_id', $request->user()->id)
            ->latest()
            ->limit(10)
            ->get();

        return view('credits.index', [
            'activePurchase' => $activePurchase,
            'recentPurchases' => $recentPurchases,
            'consultaUnitPriceCents' => (int) config('services.billing.consulta_unit_price_cents', 5),
            'boletoMinimumAmountCents' => 500,
            'boletoMinimumCreditsQuantity' => max(1, (int) ceil(500 / max(1, (int) config('services.billing.consulta_unit_price_cents', 5)))),
            'stripeConfigured' => app(StripeCheckoutService::class)->isConfigured(),
        ]);
    }

    public function store(Request $request, StripeCheckoutService $stripeCheckoutService): JsonResponse
    {
        $validated = $request->validate([
            'credits_quantity' => ['required', 'integer', 'min:1', 'max:5000'],
            'payment_method' => ['required', 'string', 'in:card,boleto'],
        ]);

        try {
            $purchase = $stripeCheckoutService->createCheckoutPurchase(
                $request->user(),
                (int) $validated['credits_quantity'],
                $validated['payment_method']
            );
        } catch (RuntimeException $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'purchase' => $this->serializePurchase($purchase),
            'credits_balance' => (int) $request->user()->fresh()->consulta_credits,
        ]);
    }

    public function show(Request $request, CreditPurchase $purchase, StripeCheckoutService $stripeCheckoutService, ConsultaCreditService $consultaCreditService): JsonResponse
    {
        abort_unless($purchase->user_id === $request->user()->id, 403);

        if ($purchase->payment_provider === 'stripe' && $purchase->status === 'pending') {
            try {
                $purchase = $stripeCheckoutService->syncPurchase($purchase);
            } catch (RuntimeException) {
            }
        }

        if ($purchase->status === 'approved') {
            $purchase = $consultaCreditService->applyApprovedPurchase($purchase);
        }

        return response()->json([
            'purchase' => $this->serializePurchase($purchase->fresh()),
            'credits_balance' => (int) $request->user()->fresh()->consulta_credits,
        ]);
    }

    protected function serializePurchase(CreditPurchase $purchase): array
    {
        $paymentMethod = (string) data_get($purchase->payment_payload, 'metadata.payment_method', 'card');

        return [
            'id' => $purchase->id,
            'payment_provider' => $purchase->payment_provider,
            'payment_method' => $paymentMethod,
            'payment_method_label' => $this->paymentMethodLabel($paymentMethod),
            'status' => $purchase->status,
            'credits_quantity' => $purchase->credits_quantity,
            'total_amount_cents' => $purchase->total_amount_cents,
            'pix_qr_code' => $purchase->pix_qr_code,
            'pix_qr_code_base64' => $purchase->pix_qr_code_base64,
            'ticket_url' => $purchase->ticket_url,
            'credited_at' => optional($purchase->credited_at)?->toIso8601String(),
            'approved_at' => optional($purchase->approved_at)?->toIso8601String(),
            'expires_at' => optional($purchase->expires_at)?->toIso8601String(),
        ];
    }

    protected function paymentMethodLabel(string $paymentMethod): string
    {
        return match ($paymentMethod) {
            'boleto' => 'Boleto',
            default => 'Cartao de credito',
        };
    }
}