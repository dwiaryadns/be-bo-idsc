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
        Schema::create('good_receipt_notes', function (Blueprint $table) {
            $table->string('grn_id')->primary();
            $table->string('penerimaan_id');
            $table->foreign('penerimaan_id')->references('penerimaan_id')->on('penerimaan_barangs')->onDelete('cascade');
            $table->string('url_file');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('good_receipt_notes');
    }
};
