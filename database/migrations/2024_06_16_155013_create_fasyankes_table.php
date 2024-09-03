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
        Schema::create('fasyankes', function (Blueprint $table) {
            $table->string('fasyankesId')->primary();
            $table->foreignId('warehouse_id')->constrained()->onDelete('cascade');
            $table->foreignId('bisnis_owner_id')->constrained()->onDelete('cascade');
            $table->string('type');
            $table->string('name');
            $table->text('address');
            $table->string('pic');
            $table->string('pic_number');
            $table->string('email');
            $table->string('latitude');
            $table->string('longitude');
            $table->boolean('is_active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fasyankes');
    }
};
