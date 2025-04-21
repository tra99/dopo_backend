<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\QuestionTypeEnum;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            // $table->bigIncrements('id')->change();
            $table->bigIncrements('id');
            $table->integer('category_id');
            $table->string('question');
            $table->string('description', 1000)->nullable();
            $table->enum('question_type', QuestionTypeEnum::values())->default(QuestionTypeEnum::TEXT->value);
            $table->json('answer_option')->nullable();  // json string
            $table->json('rule')->nullable();
            $table->timestamps();
            $table->date('published_at')->nullable();
            $table->string('school_type');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
