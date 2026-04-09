<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agendas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('processo_id')->nullable()->constrained('processos')->onDelete('cascade');
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->onDelete('cascade');
            $table->string('titulo');
            $table->string('tipo'); // audiência, reunião, prazo, despacho...
            $table->dateTime('data_inicio');
            $table->dateTime('data_fim')->nullable();
            $table->string('local')->nullable();
            $table->string('link_virtual')->nullable();
            $table->text('descricao')->nullable();
            $table->boolean('recorrente')->default(false);
            $table->enum('status', ['pendente', 'realizado', 'cancelado'])->default('pendente');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agendas');
    }
};
