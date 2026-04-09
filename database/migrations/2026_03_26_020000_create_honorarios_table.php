<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('honorarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->foreignId('processo_id')->nullable()->constrained('processos')->onDelete('cascade');
            $table->enum('tipo', ['fixo', 'mensal', 'exito', 'misto'])->default('fixo');
            $table->decimal('valor_fixo', 15, 2)->nullable();
            $table->decimal('percentual_exito', 5, 2)->nullable();
            $table->string('forma_pagamento')->nullable();
            $table->integer('dia_vencimento')->nullable();
            $table->enum('status', ['ativo', 'quitado', 'inadimplente', 'cancelado'])->default('ativo');
            $table->date('data_inicio')->nullable();
            $table->date('data_fim')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('honorarios');
    }
};
