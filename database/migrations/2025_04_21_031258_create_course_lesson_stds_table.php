<?php

// database/migrations/2025_04_21_000005_create_course_lesson_std_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('course_lesson_std', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('std_id');
            $table->unsignedInteger('lesson_id');
            $table->boolean('is_completed_quiz')->default(false);
            $table->dateTime('completed_at')->nullable();
            $table->timestamps();

            $table
                ->foreign('std_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table
                ->foreign('lesson_id')
                ->references('id')
                ->on('lessons')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_lesson_std');
    }
};

