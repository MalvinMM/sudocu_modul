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
        Schema::create('db_storeProc', function (Blueprint $table) {
            $table->id();
            $table->string('Name');
            $table->text('Description');
            $table->longText('SQL_Query');
            $table->unsignedBigInteger('ERPID');

            $table->foreign('ERPID')->references('ERPID')->on('m_erp')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('db_storeProc');
    }
};
