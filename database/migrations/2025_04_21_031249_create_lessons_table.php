<?php

// database/migrations/2025_04_21_000004_create_lessons_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lessons', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('course_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('link_video')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->unsignedInteger('quiz_total_score')->default(0);
            $table->timestamps();

            $table
                ->foreign('course_id')
                ->references('id')
                ->on('course')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};

