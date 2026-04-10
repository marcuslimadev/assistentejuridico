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
        'processo_id', 'cliente_id', 'user_id',
        'google_calendar_event_id', 'google_calendar_synced_at', 'google_calendar_sync_error'
    ];

    protected function casts(): array
    {
        return [
            'data_inicio' => 'datetime',
            'data_fim' => 'datetime',
            'recorrente' => 'boolean',
            'google_calendar_synced_at' => 'datetime',
        ];
    }

    public function processo() { return $this->belongsTo(Processo::class); }
    public function cliente() { return $this->belongsTo(Cliente::class); }
    public function usuario() { return $this->belongsTo(User::class, 'user_id'); }
}
