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
        Schema::create('school_participants', function (Blueprint $table) {
            $table->id();
            $table->integer('school_id');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');

            $table->integer('mission_id');
            $table->foreign('mission_id')->references('id')->on('missions')->onDelete('cascade');

            $table->string('organization');
            $table->integer('number_of_male')->default(0);
            $table->integer('number_of_female')->default(0);

            $table->string('file_uri')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_participants');
    }
};
