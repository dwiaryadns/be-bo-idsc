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
        Schema::create('diskons', function (Blueprint $table) {
            $table->id();
            $table->string('stok_barang_id');
            $table->foreign('stok_barang_id')->references('stok_barang_id')->on('stock_barangs')->onDelete('cascade');
            $table->string('type');
            $table->integer('percent_disc')->nullable();
            $table->decimal('amount_disc', 10, 2)->nullable();
            $table->date('expired_disc');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diskons');
    }
};
