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
        Schema::create('distribusis', function (Blueprint $table) {
            $table->string('distribusi_id')->primary();
            $table->foreignId('warehouse_id')->constrained()->onDelete('cascade');
            $table->string('fasyankes_id');
            $table->foreign('fasyankes_id')->references('fasyankesId')->on('fasyankes')->onDelete('cascade');
            $table->date('date');
            $table->string('status');
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distribusis');
    }
};
