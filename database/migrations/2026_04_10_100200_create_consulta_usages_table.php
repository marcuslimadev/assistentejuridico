<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consulta_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('provider')->default('datajud');
            $table->string('process_number')->nullable()->index();
            $table->string('status')->default('completed');
            $table->unsignedInteger('credits_consumed')->default(1);
            $table->unsignedInteger('unit_price_cents')->default(5);
            $table->unsignedInteger('balance_before');
            $table->unsignedInteger('balance_after');
            $table->text('response_excerpt')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consulta_usages');
    }
};