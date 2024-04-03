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
        Schema::create('d_report', function (Blueprint $table) {
            $table->id('ReportDetailID');
            $table->unsignedBigInteger('ReportID');
            $table->integer('Sequence');
            $table->longText('Description');
            $table->string('FilePath')->nullable();

            // $table->unsignedBigInteger('CreateUserID')->nullable();
            // $table->dateTime('CreateDateTime')->nullable();
            // $table->unsignedBigInteger('UpdateUserID')->nullable();
            // $table->dateTime('UpdateDateTime')->nullable();

            $table->foreign('ReportID')->references('ReportID')->on('m_report')->onUpdate('cascade')->onDelete('cascade');
            // $table->foreign('CreateUserID')->references('UserID')->on('m_user')->onUpdate('no action')->onDelete('no action');
            // $table->foreign('UpdateUserID')->references('UserID')->on('m_user')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('d_report');
    }
};
