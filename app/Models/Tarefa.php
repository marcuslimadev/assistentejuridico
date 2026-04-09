<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tarefa extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'titulo', 'descricao', 'prioridade', 'status',
        'prazo', 'concluida_em',
        'processo_id', 'cliente_id', 'criado_por', 'responsavel_id'
    ];

    public function processo() { return $this->belongsTo(Processo::class); }
    public function cliente() { return $this->belongsTo(Cliente::class); }
    public function criador() { return $this->belongsTo(User::class, 'criado_por'); }
    public function responsavel() { return $this->belongsTo(User::class, 'responsavel_id'); }
}
