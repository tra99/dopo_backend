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
        Schema::table('widgets', function (Blueprint $table) {
            $table->dropColumn('widget_url');
            $table->unsignedInteger('question')->nullable();
            $table->text('params')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('widgets', function (Blueprint $table) {
            $table->dropColumn('question');
            $table->dropColumn('params');
            $table->string('widget_url', 500)->nullable();
        });
    }
};
