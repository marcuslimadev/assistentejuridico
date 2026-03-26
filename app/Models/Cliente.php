<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use ClientesSoftDeletes; // Wait, actually I just need `use SoftDeletes;`
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = [
        'nome', 'tipo', 'cpf_cnpj', 'rg_ie', 'data_nascimento',
        'estado_civil', 'profissao', 'representante_legal',
        'telefone', 'celular', 'email', 'whatsapp',
        'cep', 'endereco', 'numero', 'complemento',
        'bairro', 'cidade', 'estado', 'observacoes', 'status'
    ];

    public function processos()
    {
        return $this->hasMany(Processo::class);
    }
}
