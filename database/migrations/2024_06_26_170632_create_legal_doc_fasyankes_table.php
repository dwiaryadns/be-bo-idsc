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
        Schema::create('legal_doc_fasyankes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fasyankes_id');
            $table->foreign('fasyankes_id')->references('fasyankesId')->on('fasyankes')->onDelete('cascade');
            $table->string('sia')->nullable();
            $table->string('sipa')->nullable();
            $table->string('simk')->nullable();
            $table->string('siok')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('legal_doc_fasyankes');
    }
};
