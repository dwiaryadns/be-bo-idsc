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
        Schema::create('master_kfa_povs', function (Blueprint $table) {
            $table->unsignedBigInteger('kfa_pov_code')->primary();
            $table->unsignedBigInteger('kfa_code');
            $table->foreign('kfa_code')->references('kfa_code')->on('master_kfas')->onDelete('cascade');
            $table->string('kfa_pov_idsc');
            $table->string('product_state');
            $table->text('pov_desc');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_kfa_povs');
    }
};
