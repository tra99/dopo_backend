<?php

use App\Enums\SchoolTypeEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\CategoryEnum;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string("title");
            $table->integer("parent_id")->nullable();
            $table->enum('type', CategoryEnum::values())->default(CategoryEnum::CATEGORY->value);
            $table->string('status')->default('panding');
            $table->timestamps();
            $table->foreign('parent_id')->references('id')->on('categories')->onDelete('cascade');
            $table->integer('school_type_id');
            $table->foreign('school_type_id')->references('id')->on('category_groups')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
