<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('despesas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('processo_id')->nullable()->constrained('processos')->onDelete('cascade');
            $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade');
            $table->string('descricao');
            $table->string('categoria')->nullable();
            $table->decimal('valor', 15, 2);
            $table->date('data');
            $table->boolean('reembolsavel')->default(false);
            $table->string('comprovante')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('despesas');
    }
};
