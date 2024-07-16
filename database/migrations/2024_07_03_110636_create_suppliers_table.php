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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->string('supplier_id')->primary();
            $table->foreignId('bisnis_owner_id')->constrained()->onDelete('cascade');
            $table->string('nama_supplier', 255);
            $table->string('alamat', 255);
            $table->string('kota', 100);
            $table->string('provinsi', 100);
            $table->string('kode_pos', 10);
            $table->string('negara', 100);
            $table->string('nomor_telepon', 15);
            $table->string('email', 100);
            $table->string('website', 100);
            $table->string('kontak_person', 100);
            $table->string('nomor_kontak_person', 15);
            $table->string('email_kontak_person', 100);
            $table->string('tipe_supplier', 50);
            $table->string('nomor_npwp', 20);
            $table->date('tanggal_kerjasama');
            $table->text('catatan_tambahan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
