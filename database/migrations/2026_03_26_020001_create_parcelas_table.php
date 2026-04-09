<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parcelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('honorario_id')->constrained('honorarios')->onDelete('cascade');
            $table->integer('numero');
            $table->decimal('valor', 15, 2);
            $table->date('vencimento');
            $table->boolean('pago')->default(false);
            $table->date('data_pagamento')->nullable();
            $table->decimal('valor_pago', 15, 2)->nullable();
            $table->text('observacao')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parcelas');
    }
};
