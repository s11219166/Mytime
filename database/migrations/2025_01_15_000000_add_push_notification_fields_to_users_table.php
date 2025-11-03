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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('push_notifications')->default(true)->after('weekly_reports');
            $table->text('push_subscription')->nullable()->after('push_notifications');
            $table->timestamp('last_push_notification_at')->nullable()->after('push_subscription');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['push_notifications', 'push_subscription', 'last_push_notification_at']);
        });
    }
};
