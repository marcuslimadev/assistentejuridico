<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsultaUsage extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'provider',
        'process_number',
        'status',
        'credits_consumed',
        'unit_price_cents',
        'balance_before',
        'balance_after',
        'response_excerpt',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}