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
        Schema::create('penerimaan_barangs', function (Blueprint $table) {
            $table->string('penerimaan_id')->primary();
            $table->string('po_id');
            $table->string('fasyankes_warehouse_id');
            $table->date('tanggal_penerimaan');
            $table->string('status', 50);
            $table->text('catatan');
            $table->string('penerima')->nullable();
            $table->string('pengirim')->nullable();
            $table->string('pengecek')->nullable();
            $table->foreign('po_id')->references('po_id')->on('pembelians')->onDelete('cascade');
            $table->foreign('fasyankes_warehouse_id')->references('wfid')->on('fasyankes_warehouse')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penerimaan_barangs');
    }
};
