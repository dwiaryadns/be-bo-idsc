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
        Schema::create('stok_opnames', function (Blueprint $table) {
            $table->string('stok_opname_id');
            $table->string('petugas');
            $table->string('barang_id');
            $table->foreign('barang_id')->references('barang_id')->on('barangs')->onDelete('cascade');
            $table->string('stock_gudang_id')->nullable();
            $table->foreign('stock_gudang_id')->references('stock_gudang_id')->on('stock_gudangs')->onDelete('cascade');
            $table->string('stok_barang_id')->nullable();
            $table->foreign('stok_barang_id')->references('stok_barang_id')->on('stock_barangs')->onDelete('cascade');
            $table->text('deskripsi')->nullable();
            $table->date('tanggal_opname');
            $table->integer('jml_tercatat');
            $table->integer('jml_fisik');
            $table->integer('jml_penyesuaian');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok_opnames');
    }
};
