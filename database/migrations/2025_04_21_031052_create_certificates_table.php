<?php

// database/migrations/2025_04_21_000003_create_certificate_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('certificate', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('course_id');
            $table->dateTime('date_of_certificate');
            $table->timestamps();

            $table
                ->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table
                ->foreign('course_id')
                ->references('id')
                ->on('course')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificate');
    }
};

