<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Agenda extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'titulo', 'tipo', 'data_inicio', 'data_fim', 'local',
        'link_virtual', 'descricao', 'recorrente', 'status',
        'processo_id', 'cliente_id', 'user_id'
    ];

    public function processo() { return $this->belongsTo(Processo::class); }
    public function cliente() { return $this->belongsTo(Cliente::class); }
    public function usuario() { return $this->belongsTo(User::class, 'user_id'); }
}
