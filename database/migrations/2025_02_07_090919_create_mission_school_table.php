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
        Schema::create('mission_school', function (Blueprint $table) {
            $table->id();
            $table->integer('mission_id');
            $table->foreign('mission_id')
                ->references('id')
                ->on('missions')->onDelete('cascade');
            $table->integer('school_id');
            $table->foreign('school_id')
                ->references('id')
                ->on('schools')->onDelete('cascade');

            $table->text('perspective')->nullable();
            $table->text('conclusion')->nullable();
            $table->text('appendix')->nullable();

            $table->string('report_uri')->nullable();
            $table->string('attendance_uri')->nullable();
            $table->string('assessment_uri')->nullable();
            $table->string('slide_uri')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mission_school');
    }
};
