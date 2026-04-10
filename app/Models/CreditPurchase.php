<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditPurchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'payment_provider',
        'external_reference',
        'provider_session_id',
        'provider_payment_id',
        'status',
        'credits_quantity',
        'unit_price_cents',
        'total_amount_cents',
        'payer_email',
        'pix_qr_code',
        'pix_qr_code_base64',
        'ticket_url',
        'payment_payload',
        'expires_at',
        'approved_at',
        'credited_at',
    ];

    protected function casts(): array
    {
        return [
            'payment_payload' => 'array',
            'expires_at' => 'datetime',
            'approved_at' => 'datetime',
            'credited_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}