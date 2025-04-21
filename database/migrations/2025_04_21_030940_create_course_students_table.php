<?php

// database/migrations/2025_04_21_000002_create_course_student_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('course_student', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('std_id');
            $table->unsignedInteger('course_id');
            $table->unsignedInteger('completion_percentage')->default(0);
            $table->timestamps();

            $table
                ->foreign('std_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table
                ->foreign('course_id')
                ->references('id')
                ->on('course')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_student');
    }
};

