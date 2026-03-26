<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Processo extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = [
        'cliente_id', 'advogado_id', 'numero_cnj', 'tipo_acao',
        'area_direito', 'status', 'vara', 'comarca', 'tribunal',
        'juiz', 'partes_contrarias', 'polo', 'valor_causa',
        'data_distribuicao'
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function advogado()
    {
        return $this->belongsTo(User::class, 'advogado_id');
    }
}
