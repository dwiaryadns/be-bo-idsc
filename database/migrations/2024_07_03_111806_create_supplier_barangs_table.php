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
        Schema::create('supplier_barangs', function (Blueprint $table) {
            $table->string('supplier_barang_id')->primary();
            $table->string('supplier_id');
            $table->string('barang_id');
            $table->decimal('harga', 10, 2);
            $table->date('tanggal_mulai');
            $table->date('tanggal_berakhir');
            $table->foreign('supplier_id')->references('supplier_id')->on('suppliers')->onDelete('cascade');
            $table->foreign('barang_id')->references('barang_id')->on('barangs')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_barangs');
    }
};
