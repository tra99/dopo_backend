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
        Schema::table('school_survey', function (Blueprint $table) {
            $table->date('date')->nullable();
            $table->string('start_time')->nullable();
            $table->string('interviewer')->nullable();
            $table->string('organization')->nullable();
            $table->string('interviewer_phone')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('school_survey', function (Blueprint $table) {
            $table->dropColumn('date');
            $table->dropColumn('start_time');
            $table->dropColumn('interviewer');
            $table->dropColumn('organization');
            $table->dropColumn('interviewer_phone');
        });
    }
};
