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
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();

            // Evaluate on which criteria
            $table->integer('evaluation_criteria_id');
            $table->foreign('evaluation_criteria_id')
                ->references('id')
                ->on('evaluation_criterias')->onDelete('cascade');
            // Evaluate on which school
            $table->integer('school_id');
            $table->foreign('school_id')
                ->references('id')
                ->on('schools')->onDelete('cascade');
            // Evaluate in which mission
            $table->integer('mission_id');
            $table->foreign('mission_id')
                ->references('id')
                ->on('missions')->onDelete('cascade');
            // What is the result
            $table->string('result');
            // which equivalent to score
            $table->decimal('score', total: 5, places: 2);
            // Elaborate the result
            $table->text('description')->nullable();
            // In case there are file attachments
            $table->string('documents')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};
