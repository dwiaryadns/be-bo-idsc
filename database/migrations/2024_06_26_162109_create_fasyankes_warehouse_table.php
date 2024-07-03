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
        Schema::create('fasyankes_warehouse', function (Blueprint $table) {
            $table->string('wfid')->primary();
            $table->unsignedBigInteger('fasyankes_id');
            $table->foreign('fasyankes_id')->references('fasyankesId')->on('fasyankes')->onDelete('cascade');
            $table->foreignId('warehouse_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fasyankes_warehouse');
    }
};
