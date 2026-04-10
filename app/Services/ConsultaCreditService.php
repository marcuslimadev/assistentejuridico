<?php

namespace App\Services;

use App\Models\CreditPurchase;
use App\Models\ConsultaUsage;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ConsultaCreditService
{
    public function unitPriceCents(): int
    {
        return (int) config('services.billing.consulta_unit_price_cents', 5);
    }

    public function ensureAvailable(User $user, int $quantity = 1): void
    {
        if ($user->consulta_credits < $quantity) {
            throw new RuntimeException('Você não possui créditos suficientes para consultar este processo.');
        }
    }

    public function consumeForDataJud(User $user, string $processNumber, string $responseExcerpt, array $meta = []): User
    {
        return DB::transaction(function () use ($user, $processNumber, $responseExcerpt, $meta) {
            $lockedUser = User::query()->lockForUpdate()->findOrFail($user->id);

            $this->ensureAvailable($lockedUser);

            $before = (int) $lockedUser->consulta_credits;
            $after = $before - 1;

            $lockedUser->forceFill([
                'consulta_credits' => $after,
            ])->save();

            ConsultaUsage::create([
                'user_id' => $lockedUser->id,
                'provider' => 'datajud',
                'process_number' => $processNumber,
                'status' => 'completed',
                'credits_consumed' => 1,
                'unit_price_cents' => $this->unitPriceCents(),
                'balance_before' => $before,
                'balance_after' => $after,
                'response_excerpt' => mb_substr($responseExcerpt, 0, 2000),
                'meta' => $meta,
            ]);

            return $lockedUser->fresh();
        });
    }

    public function creditPurchase(User $user, int $quantity): User
    {
        return DB::transaction(function () use ($user, $quantity) {
            $lockedUser = User::query()->lockForUpdate()->findOrFail($user->id);
            $lockedUser->increment('consulta_credits', $quantity);
            return $lockedUser->fresh();
        });
    }

    public function applyApprovedPurchase(CreditPurchase $purchase): CreditPurchase
    {
        return DB::transaction(function () use ($purchase) {
            $lockedPurchase = CreditPurchase::query()->lockForUpdate()->findOrFail($purchase->id);

            if ($lockedPurchase->status !== 'approved' || $lockedPurchase->credited_at) {
                return $lockedPurchase;
            }

            $lockedUser = User::query()->lockForUpdate()->findOrFail($lockedPurchase->user_id);
            $lockedUser->increment('consulta_credits', $lockedPurchase->credits_quantity);

            $lockedPurchase->forceFill([
                'credited_at' => now(),
            ])->save();

            return $lockedPurchase->fresh();
        });
    }
}