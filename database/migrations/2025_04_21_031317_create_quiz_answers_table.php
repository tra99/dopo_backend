<?php

// database/migrations/2025_04_21_000007_create_quiz_answers_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('quiz_answers', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('std_id');
            $table->unsignedInteger('questions_id');
            $table->integer('point');
            $table->string('std_answer')->nullable();
            $table->string('correct_answer')->nullable();
            $table->unsignedInteger('lesson_id');
            $table->timestamps();

            $table
                ->foreign('std_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table
                ->foreign('questions_id')
                ->references('id')
                ->on('quiz_questions')
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
        Schema::dropIfExists('quiz_answers');
    }
};

