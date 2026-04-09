<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocTemplate extends Model
{
    protected $fillable = [
        'nome', 'categoria', 'conteudo_html', 'campos_dinamicos'
    ];

    protected $casts = [
        'campos_dinamicos' => 'array'
    ];
}
