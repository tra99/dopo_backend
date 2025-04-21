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
        Schema::table('users', function (Blueprint $table) {
            $table->integer('school_id')->nullable();
            $table->boolean('status')->default(false);
            $table->dateTime('lastest_login')->nullable();
            $table->string('avatar')->nullable();
            $table->string('fcm_token')->nullable()->after('remember_token');
            $table->string('description')->nullable();

            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->boolean('status')->default(false);
            $table->timestamps();
        });

        Schema::create('user_role', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('role_id');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->timestamps();
        });
    }



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('school_id');
        });
        Schema::dropIfExists('roles');
        Schema::dropIfExists('user_role');
    }
};
