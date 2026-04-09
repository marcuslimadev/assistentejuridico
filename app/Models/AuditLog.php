<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id', 'acao', 'tabela', 'registro_id',
        'dados_antes', 'dados_depois', 'ip'
    ];

    protected $casts = [
        'dados_antes' => 'array',
        'dados_depois' => 'array'
    ];
}
