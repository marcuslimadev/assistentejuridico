<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prazos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('processo_id')->constrained('processos')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('descricao');
            $table->date('data_intimacao')->nullable();
            $table->date('data_prazo');
            $table->string('tipo_prazo'); // fatal, útil, em dobro...
            $table->enum('status', ['pendente', 'em andamento', 'cumprido', 'vencido', 'suspenso'])->default('pendente');
            $table->dateTime('cumprido_em')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prazos');
    }
};
