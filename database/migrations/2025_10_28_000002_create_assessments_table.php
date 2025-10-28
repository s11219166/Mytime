<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->decimal('actual_percentage', 5, 2);
            $table->decimal('target_percentage', 5, 2);
            $table->decimal('achieved_percentage', 5, 2)->nullable();
            $table->decimal('target_gpa', 3, 2)->virtualAs('CASE
                WHEN target_percentage >= 90 THEN 4.0
                WHEN target_percentage >= 80 THEN 3.0
                WHEN target_percentage >= 70 THEN 2.0
                WHEN target_percentage >= 60 THEN 1.0
                ELSE 0.0
            END');
            $table->decimal('achieved_gpa', 3, 2)->virtualAs('CASE
                WHEN achieved_percentage >= 90 THEN 4.0
                WHEN achieved_percentage >= 80 THEN 3.0
                WHEN achieved_percentage >= 70 THEN 2.0
                WHEN achieved_percentage >= 60 THEN 1.0
                ELSE 0.0
            END');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('assessments');
    }
};
