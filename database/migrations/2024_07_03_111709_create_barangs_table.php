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
        Schema::create('barangs', function (Blueprint $table) {
            $table->string('barang_id')->primary();
            $table->string('kategori_id')->nullable();
            $table->string('nama_barang');
            $table->string('supplier_id');
            $table->foreign('supplier_id')->references('supplier_id')->on('suppliers')->onDelete('cascade');
            $table->unsignedBigInteger('kfa_poa_code')->nullable();
            $table->foreign('kategori_id')->references('kategori_id')->on('kategori_barang_apoteks')->onDelete('cascade');
            $table->foreign('kfa_poa_code')->references('kfa_poa_code')->on('master_kfa_poas')->onDelete('cascade');
            $table->string('satuan');
            $table->decimal('harga_beli', 10, 2);
            $table->decimal('harga_jual', 10, 2);
            $table->date('expired_at');
            $table->text('deskripsi');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barangs');
    }
};
