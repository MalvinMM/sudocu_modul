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
        Schema::create('m_user', function (Blueprint $table) {
            $table->id('UserID');
            $table->string('UserName', 20);
            $table->string('FullName', 100);
            $table->string('NIK', 10)->unique();
            $table->string('password');
            $table->string('Role');
            $table->boolean('isActive')->default(true);
            $table->unsignedBigInteger('CreateUserID')->nullable();
            $table->dateTime('CreateDateTime')->nullable();
            $table->unsignedBigInteger('UpdateUserID')->nullable();
            $table->dateTime('UpdateDateTime')->nullable();

            $table->foreign('CreateUserID')->references('UserID')->on('m_user')->onUpdate('no action')->onDelete('no action');
            $table->foreign('UpdateUserID')->references('UserID')->on('m_user')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
