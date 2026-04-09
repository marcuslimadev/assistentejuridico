<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prazo extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'descricao', 'data_intimacao', 'data_prazo', 'tipo_prazo',
        'status', 'cumprido_em', 'observacoes',
        'processo_id', 'user_id'
    ];

    public function processo() { return $this->belongsTo(Processo::class); }
    public function usuario() { return $this->belongsTo(User::class, 'user_id'); }
}
