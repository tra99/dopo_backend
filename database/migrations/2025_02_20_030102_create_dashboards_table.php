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
        Schema::create('dashboards', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('dashboard_type');
            $table->text('description')->nullable();
            $table->boolean('is_draggable')->default(true);
            $table->boolean('is_resizable')->default(true);
            $table->boolean('is_bounded')->default(true);
            $table->timestamps();

            $table->string('icon')->nullable();
            $table->string('dashboard_url')->nullable();    // Link with metabase dashboard URL
            $table->integer('parent_id')->nullable();
            $table->foreign('parent_id')->references('id')->on('dashboards')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dashboards');
    }
};
