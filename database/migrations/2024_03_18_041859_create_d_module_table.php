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
        Schema::create('d_module', function (Blueprint $table) {
            $table->id('ModuleDetailID');
            $table->unsignedBigInteger('ModuleID');
            $table->integer('Sequence');
            $table->longText('Description');
            $table->string('FilePath')->nullable();

            $table->unsignedBigInteger('CreateUserID')->nullable();
            $table->dateTime('CreateDateTime')->nullable();
            $table->unsignedBigInteger('UpdateUserID')->nullable();
            $table->dateTime('UpdateDateTime')->nullable();

            $table->foreign('ModuleID')->references('ModuleID')->on('m_module')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('CreateUserID')->references('UserID')->on('m_user')->onUpdate('no action')->onDelete('no action');
            $table->foreign('UpdateUserID')->references('UserID')->on('m_user')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('d_module');
    }
};
