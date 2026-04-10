<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('credit_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->uuid('external_reference')->unique();
            $table->string('mercado_pago_payment_id')->nullable()->unique();
            $table->string('status')->default('pending');
            $table->unsignedInteger('credits_quantity');
            $table->unsignedInteger('unit_price_cents')->default(5);
            $table->unsignedInteger('total_amount_cents');
            $table->string('payer_email')->nullable();
            $table->longText('pix_qr_code')->nullable();
            $table->longText('pix_qr_code_base64')->nullable();
            $table->text('ticket_url')->nullable();
            $table->json('payment_payload')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('credited_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('credit_purchases');
    }
};