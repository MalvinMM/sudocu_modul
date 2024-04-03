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
        Schema::create('d_table', function (Blueprint $table) {
            $table->id('FieldID');
            $table->unsignedBigInteger('TableID');
            $table->string('Name', 50);
            $table->longText('Description')->nullable();
            $table->string('DataType', 50);
            $table->string('AllowNull', 3);
            $table->string('DefaultValue', 200)->nullable();

            $table->foreign('TableID')->references('TableID')->on('m_table')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('d_table');
    }
};
