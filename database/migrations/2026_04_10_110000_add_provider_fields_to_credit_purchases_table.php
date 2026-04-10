<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('credit_purchases', function (Blueprint $table) {
            $table->string('payment_provider')->nullable()->after('user_id');
            $table->string('provider_session_id')->nullable()->unique()->after('external_reference');
            $table->string('provider_payment_id')->nullable()->unique()->after('provider_session_id');
        });
    }

    public function down(): void
    {
        Schema::table('credit_purchases', function (Blueprint $table) {
            $table->dropUnique(['provider_session_id']);
            $table->dropUnique(['provider_payment_id']);
            $table->dropColumn(['payment_provider', 'provider_session_id', 'provider_payment_id']);
        });
    }
};