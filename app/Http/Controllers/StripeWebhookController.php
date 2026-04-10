<?php

namespace App\Http\Controllers;

use App\Services\ConsultaCreditService;
use App\Services\StripeCheckoutService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StripeWebhookController extends Controller
{
    public function __invoke(Request $request, StripeCheckoutService $stripeCheckoutService, ConsultaCreditService $consultaCreditService): JsonResponse
    {
        if (! $stripeCheckoutService->validateWebhookSignature($request)) {
            Log::warning('Stripe webhook rejected due to invalid signature.', [
                'signature' => $request->header('Stripe-Signature'),
            ]);

            return response()->json(['received' => false], 403);
        }

        $event = $stripeCheckoutService->parseWebhookEvent($request);

        if (! $event) {
            return response()->json(['received' => false], 400);
        }

        $type = (string) ($event['type'] ?? '');
        $session = data_get($event, 'data.object');

        if (! is_array($session)) {
            return response()->json(['received' => true]);
        }

        $overrideStatus = match ($type) {
            'checkout.session.async_payment_succeeded' => 'approved',
            'checkout.session.async_payment_failed' => 'failed',
            'checkout.session.expired' => 'expired',
            default => null,
        };

        $purchase = $stripeCheckoutService->syncPurchaseFromSessionData($session, $overrideStatus);

        if ($purchase && $purchase->status === 'approved') {
            $consultaCreditService->applyApprovedPurchase($purchase);
        }

        return response()->json(['received' => true]);
    }
}