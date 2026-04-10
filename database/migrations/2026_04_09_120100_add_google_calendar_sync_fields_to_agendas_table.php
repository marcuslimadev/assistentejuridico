<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agendas', function (Blueprint $table) {
            $table->string('google_calendar_event_id')->nullable()->after('user_id');
            $table->timestamp('google_calendar_synced_at')->nullable()->after('google_calendar_event_id');
            $table->text('google_calendar_sync_error')->nullable()->after('google_calendar_synced_at');
        });
    }

    public function down(): void
    {
        Schema::table('agendas', function (Blueprint $table) {
            $table->dropColumn([
                'google_calendar_event_id',
                'google_calendar_synced_at',
                'google_calendar_sync_error',
            ]);
        });
    }
};