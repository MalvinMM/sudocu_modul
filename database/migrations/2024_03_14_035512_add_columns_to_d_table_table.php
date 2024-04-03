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
        Schema::table('d_table', function (Blueprint $table) {
            $table->unsignedBigInteger('TableIDRef')->nullable();
            $table->unsignedBigInteger('FieldIDRef')->nullable();

            $table->foreign('TableIDRef')->references('TableID')->on('m_table')->onUpdate('no action')->onDelete('no action');
            $table->foreign('FieldIDRef')->references('FieldID')->on('d_table')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('d_table', function ($table) {
            $table->dropForeign(['TableIDRef']);
            $table->dropForeign(['FieldIDRef']);
            $table->dropColumn('TableIDRef');
            $table->dropColumn('FieldIDRef');
        });
    }
};
