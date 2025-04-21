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
        // Rename the table from 'achieve_category_or_support_phase' to 'achievement_details'
        Schema::rename('achieve_category_or_support_phase', 'achievement_details');

        // Now, add the 'score' column to the renamed table
        Schema::table('achievement_details', function (Blueprint $table) {
            $table->decimal('score', 10, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First, remove the 'score' column
        Schema::table('achievement_details', function (Blueprint $table) {
            $table->dropColumn('score');
        });

        // Then, rename the table back to its original name
        Schema::rename('achievement_details', 'achieve_category_or_support_phase');
    }
};
