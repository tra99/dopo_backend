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
        Schema::create('achieve_category_or_support_phase', function (Blueprint $table) {
            $table->id();
            $table->integer('achievement_id');
            $table->integer('category_id')->nullable();
            $table->integer('support_phase_id')->nullable();
            $table->text('description')->nullable();

            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('support_phase_id')->references('id')->on('support_phases')->onDelete('cascade');
            $table->foreign('achievement_id')->references('id')->on('achievements')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('achieve_category_or_support_phase');
    }
};
