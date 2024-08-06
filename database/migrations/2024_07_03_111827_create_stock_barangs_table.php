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
        Schema::create('stock_barangs', function (Blueprint $table) {
            $table->string('stok_barang_id')->primary();
            $table->string('fasyankes_warehouse_id');
            $table->string('barang_id');
            $table->integer('stok');
            $table->integer('stok_min')->nullable();
            $table->foreign('fasyankes_warehouse_id')->references('wfid')->on('fasyankes_warehouse')->onDelete('cascade');
            $table->foreign('barang_id')->references('barang_id')->on('barangs')->onDelete('cascade');
            $table->decimal('harga_jual', 10, 2)->default(0)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_barangs');
    }
};
