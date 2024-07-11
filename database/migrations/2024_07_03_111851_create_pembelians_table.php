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
        Schema::create('pembelians', function (Blueprint $table) {
            $table->string('po_id')->primary();
            $table->string('supplier_id');
            $table->string('po_name');
            $table->string('fasyankes_warehouse_id');
            $table->date('tanggal_po');
            $table->string('status', 50);
            $table->decimal('total_harga', 10, 2);
            $table->text('catatan')->nullable();
            $table->foreign('supplier_id')->references('supplier_id')->on('suppliers')->onDelete('cascade');
            $table->foreign('fasyankes_warehouse_id')->references('wfid')->on('fasyankes_warehouse')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembelians');
    }
};
