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
        Schema::create('schools', function (Blueprint $table) {
            $table->increments('id');
            $table->string('school_code');
            $table->string('school_name_kh');
            $table->string('school_name_en')->nullable();
            $table->string('school_type_kh');
            $table->string('school_type_en')->nullable();
            $table->string('sis')->nullable();
            $table->string('village_kh');
            $table->string('village_en')->nullable();
            $table->string('commune_kh');
            $table->string('commune_en')->nullable();
            $table->string('district_kh');
            $table->string('district_en')->nullable();
            $table->string('province_kh');
            $table->string('province_en')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schools');
    }
};
