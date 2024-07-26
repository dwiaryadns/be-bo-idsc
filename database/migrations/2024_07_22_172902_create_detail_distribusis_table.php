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
        Schema::create('detail_distribusis', function (Blueprint $table) {
            $table->string('detail_distribusi_id')->primary();
            $table->string('distribusi_id');
            $table->foreign('distribusi_id')->references('distribusi_id')->on('distribusis')->onDelete('cascade');
            $table->string('barang_id');
            $table->foreign('barang_id')->references('barang_id')->on('barangs')->onDelete('cascade');
            $table->string('jumlah');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_distribusis');
    }
};
