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
        Schema::create('challenges', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->integer('school_id');
            $table->foreign('school_id')
                ->references('id')
                ->on('schools')->onDelete('cascade');

            $table->integer('mission_id');
            $table->foreign('mission_id')
                ->references('id')
                ->on('missions')->onDelete('cascade');

            $table->integer('category_id');
            $table->foreign('category_id')
                ->references('id')
                ->on('categories')->onDelete('cascade');

            $table->text('challenge');
            $table->text('solution')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('challenges');
    }
};
