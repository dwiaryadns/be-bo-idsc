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
        Schema::create('detail_penerimaan_barangs', function (Blueprint $table) {
            $table->string('detil_penerimaan_id')->primary();
            $table->string('penerimaan_id');
            $table->string('barang_id');
            $table->integer('jumlah');
            $table->integer('jml_datang');
            $table->integer('jml_kurang');
            $table->string('kondisi', 100);
            $table->foreign('penerimaan_id')->references('penerimaan_id')->on('penerimaan_barangs')->onDelete('cascade');
            $table->foreign('barang_id')->references('barang_id')->on('barangs')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_penerimaan_barangs');
    }
};
