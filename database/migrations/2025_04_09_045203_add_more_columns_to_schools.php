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
        Schema::table('schools', function (Blueprint $table) {
            $table->string('principal_name_kh')->nullable();
            $table->string('principal_name_en')->nullable();
            $table->string('principal_gender')->nullable();
            $table->string('principal_phone')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->dropColumn('principal_name_kh');
            $table->dropColumn('principal_name_en');
            $table->dropColumn('principal_gender');
            $table->dropColumn('principal_phone');
        });
    }
};
