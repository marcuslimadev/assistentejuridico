<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Parcela extends Model
{
    protected $fillable = [
        'honorario_id', 'numero', 'valor', 'vencimento',
        'pago', 'data_pagamento', 'valor_pago', 'observacao'
    ];

    public function honorario() { return $this->belongsTo(Honorario::class); }
}
