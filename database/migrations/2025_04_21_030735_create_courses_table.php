<?php

// database/migrations/2025_04_21_000001_create_course_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('course', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->unsignedInteger('teacher_id');
            $table->dateTime('completion_date')->nullable();
            $table->text('description')->nullable();
            $table->text('image')->nullable();
            $table->timestamps();

            $table
                ->foreign('teacher_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course');
    }
};
