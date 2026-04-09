<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Documento extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'processo_id', 'usuario_id', 'nome', 'categoria',
        'caminho', 'tamanho', 'tipo_mime', 'versao'
    ];

    public function processo() { return $this->belongsTo(Processo::class); }
    public function usuario() { return $this->belongsTo(User::class, 'usuario_id'); }
}
