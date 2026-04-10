<?php

namespace App\Http\Controllers;

use App\Services\ConsultaCreditService;
use App\Services\MercadoPagoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;

class MercadoPagoWebhookController extends Controller
{
    public function __invoke(Request $request, MercadoPagoService $mercadoPagoService, ConsultaCreditService $consultaCreditService): JsonResponse
    {
        $paymentId = $request->input('data.id')
            ?? $request->input('id')
            ?? $request->input('resource.id');

        if (! $paymentId) {
            return response()->json(['received' => true]);
        }

        try {
            $purchase = $mercadoPagoService->syncPurchaseByPaymentId((string) $paymentId);
        } catch (RuntimeException) {
            return response()->json(['received' => true]);
        }

        if ($purchase && $purchase->status === 'approved') {
            $consultaCreditService->applyApprovedPurchase($purchase);
        }

        return response()->json(['received' => true]);
    }
}