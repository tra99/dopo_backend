<?php

// database/migrations/2025_04_21_000006_create_quiz_questions_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->increments('id');
            $table->text('question_text');
            $table->string('type');              // e.g. 'multiple_choice'
            $table->json('answer_options')->nullable();  // store [{ "ans": "a", "point": 1 }, …]
            $table->unsignedInteger('lesson_id');
            $table->text('description')->nullable();
            $table->timestamps();

            $table
                ->foreign('lesson_id')
                ->references('id')
                ->on('lessons')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_questions');
    }
};

