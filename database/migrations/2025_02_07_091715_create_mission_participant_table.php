<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mission_participant', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('mission_id');
            $table->foreign('mission_id')
                ->references('id')
                ->on('missions')->onDelete('cascade');
            $table->integer('participant_id');
            $table->foreign('participant_id')
                ->references('id')
                ->on('participants')->onDelete('cascade');
            $table->string('role')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mission_participant');
    }
};
