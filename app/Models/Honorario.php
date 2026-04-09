<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Honorario extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'processo_id', 'cliente_id', 'tipo', 'valor_fixo',
        'percentual_exito', 'forma_pagamento', 'dia_vencimento',
        'status', 'data_inicio', 'data_fim'
    ];

    public function processo() { return $this->belongsTo(Processo::class); }
    public function cliente() { return $this->belongsTo(Cliente::class); }
    public function parcelas() { return $this->hasMany(Parcela::class); }
}
