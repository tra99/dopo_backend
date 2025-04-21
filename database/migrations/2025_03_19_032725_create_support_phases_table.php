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
        Schema::create('support_phases', function (Blueprint $table) {
            $table->id();
            $table->string('number')->nullable();
            $table->string('title');
            $table->integer('parent_id')->nullable();
            $table->string('status')->default('panding');
            $table->integer('school_type_id');
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('support_phases')->onDelete('cascade');
            $table->foreign('school_type_id')->references('id')->on('category_groups')->onDelete('cascade');
           
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_phases');
    }
};
