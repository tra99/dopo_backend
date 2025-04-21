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
        Schema::create('widgets', function (Blueprint $table) {
            $table->id();
            $table->integer('dashboard_id');
            $table->string('title');
            $table->integer('axis_x');
            $table->integer('axis_y');
            $table->integer('width');
            $table->integer('height');
            $table->integer('sort');
            $table->text('description')->nullable();
            $table->string('widget_url', 500)->nullable();  // Link with metabase widget URL
            $table->string('component')->nullable();        // Component name for Custom VueJS

            $table->foreign('dashboard_id')->references('id')->on('dashboards')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('widgets');
    }
};
