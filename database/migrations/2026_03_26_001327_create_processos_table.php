<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('processos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->foreignId('advogado_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('numero_cnj')->unique()->nullable();
            $table->string('tipo_acao')->nullable();
            $table->string('area_direito')->nullable();
            $table->enum('status', ['em andamento', 'suspenso', 'arquivado', 'encerrado', 'ganho', 'perdido'])->default('em andamento');
            $table->string('vara')->nullable();
            $table->string('comarca')->nullable();
            $table->string('tribunal')->nullable();
            $table->string('juiz')->nullable();
            $table->text('partes_contrarias')->nullable();
            $table->enum('polo', ['ativo', 'passivo', 'terceiro'])->default('ativo');
            $table->decimal('valor_causa', 15, 2)->nullable();
            $table->date('data_distribuicao')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('processos');
    }
};
