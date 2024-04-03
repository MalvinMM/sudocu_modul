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
        Schema::create('m_report', function (Blueprint $table) {
            $table->id('ReportID');
            $table->unsignedBigInteger('ERPID');
            $table->unsignedBigInteger('CategoryID');
            $table->string('Name', 100);
            $table->longText('Description');

            $table->unsignedBigInteger('CreateUserID')->nullable();
            $table->dateTime('CreateDateTime')->nullable();
            $table->unsignedBigInteger('UpdateUserID')->nullable();
            $table->dateTime('UpdateDateTime')->nullable();

            $table->foreign('ERPID')->references('ERPID')->on('m_erp')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('CategoryID')->references('CategoryID')->on('m_report_category')->onUpdate('no action')->onDelete('no action');
            $table->foreign('CreateUserID')->references('UserID')->on('m_user')->onUpdate('no action')->onDelete('no action');
            $table->foreign('UpdateUserID')->references('UserID')->on('m_user')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_report');
    }
};
