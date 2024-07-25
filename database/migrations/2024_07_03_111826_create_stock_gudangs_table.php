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
        Schema::create('stock_gudangs', function (Blueprint $table) {
            $table->string('stock_gudang_id')->primary();
            $table->foreignId('warehouse_id')->constrained()->onDelete('cascade');
            $table->string('barang_id');
            $table->foreign('barang_id')->references('barang_id')->on('barangs')->onDelete('cascade');
            $table->integer('stok');
            $table->boolean('isJual')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_gudangs');
    }
};
