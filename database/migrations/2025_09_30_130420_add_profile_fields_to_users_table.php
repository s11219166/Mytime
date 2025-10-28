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
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('department')->nullable();
            $table->string('position')->nullable();
            $table->text('bio')->nullable();
            $table->string('timezone')->default('UTC-05:00 (Eastern Time)');
            $table->string('date_format')->default('MM/DD/YYYY');
            $table->string('time_format')->default('12 Hour (AM/PM)');
            $table->integer('working_hours')->default(8);
            $table->boolean('email_notifications')->default(true);
            $table->boolean('project_updates')->default(true);
            $table->boolean('time_reminders')->default(true);
            $table->boolean('weekly_reports')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'first_name',
                'last_name',
                'phone',
                'department',
                'position',
                'bio',
                'timezone',
                'date_format',
                'time_format',
                'working_hours',
                'email_notifications',
                'project_updates',
                'time_reminders',
                'weekly_reports'
            ]);
        });
    }
};
