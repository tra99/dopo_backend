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
        Schema::table('missions', function (Blueprint $table) {
            $table->text('perspective')->nullable();
            $table->text('conclusion')->nullable();
            $table->text('appendix')->nullable();

            $table->string('report_uri')->nullable();
            $table->string('attendance_uri')->nullable();
            $table->string('assessment_uri')->nullable();
            $table->string('slide_uri')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('missions', function (Blueprint $table) {
            $table->dropColumn('perspective');
            $table->dropColumn('conclusion');
            $table->dropColumn('appendix');
            $table->dropColumn('report_uri');
            $table->dropColumn('attendance_uri');
            $table->dropColumn('assessment_uri');
            $table->dropColumn('slide_uri');
        });
    }
};
