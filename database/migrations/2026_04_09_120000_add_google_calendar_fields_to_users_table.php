<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->longText('google_calendar_token')->nullable()->after('celular');
            $table->text('google_calendar_refresh_token')->nullable()->after('google_calendar_token');
            $table->timestamp('google_calendar_token_expires_at')->nullable()->after('google_calendar_refresh_token');
            $table->string('google_calendar_calendar_id')->nullable()->after('google_calendar_token_expires_at');
            $table->timestamp('google_calendar_connected_at')->nullable()->after('google_calendar_calendar_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'google_calendar_token',
                'google_calendar_refresh_token',
                'google_calendar_token_expires_at',
                'google_calendar_calendar_id',
                'google_calendar_connected_at',
            ]);
        });
    }
};