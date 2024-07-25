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
            $table->string('nama_supplier');
            $table->string('alamat');
            $table->string('kabupaten');
            $table->string('provinsi');
            $table->string('desa');
            $table->string('kecamatan');
            $table->string('kode_pos');
            $table->string('nomor_npwp');
            $table->string('nomor_telepon');
            $table->string('email');
            $table->string('website')->nullable();
            $table->string('kontak_person');
            $table->string('nomor_kontak_person');
            $table->string('email_kontak_person');
            $table->string('tipe_supplier');
            $table->date('start_pks_date');
            $table->date('end_pks_date');
            $table->text('catatan_tambahan')->nullable();
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
