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
        Schema::table('dashboards', function (Blueprint $table) {
            $table->dropColumn('dashboard_url');
            $table->unsignedInteger('dashboard')->nullable();
            $table->text('params')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dashboards', function (Blueprint $table) {
            $table->dropColumn('dashboard');
            $table->dropColumn('params');
            $table->string('dashboard_url')->nullable();
        });
    }
};
