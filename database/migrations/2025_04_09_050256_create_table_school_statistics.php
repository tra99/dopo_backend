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
        Schema::create('school_statistics', function (Blueprint $table) {
            $table->id();
            $table->integer('academic_year');
            $table->integer('count_enrollment')->default(0);
            $table->integer('count_female_enrollment')->default(0);
            $table->integer('count_teaching_staff')->default(0);
            $table->integer('count_female_teaching_staff')->default(0);
            $table->integer('count_not_teaching_staff')->default(0);
            $table->integer('count_female_not_teaching_staff')->default(0);
            $table->integer('count_staff')->default(0);
            $table->integer('count_female_staff')->default(0);
            $table->enum('electricity', ['Private', 'State', 'None'])->default('State');
            $table->integer('count_computer')->default(0);
            $table->boolean('hasInternet')->default(false);
            $table->timestamps();

            $table->unsignedBigInteger('school_id');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_statistics');
    }
};
