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
        Schema::create('access_fasyankes', function (Blueprint $table) {
            $table->id();
            $table->string('fasyankes_id');
            $table->foreign('fasyankes_id')->references('fasyankesId')->on('fasyankes')->onDelete('cascade');
            $table->string('username')->unique();
            $table->string('password');
            $table->boolean('is_active');
            $table->string('created_by');
            $table->string('id_profile')->nullable();
            $table->string('role')->default('admin');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('access_fasyankes');
    }
};
