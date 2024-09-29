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
        Schema::create('bo_infos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bisnis_owner_id')->constrained();
            $table->string('businessId')->constrained();
            $table->string('businessType');
            $table->string('businessName');
            $table->string('businessEmail')->unique();
            $table->string('phone');
            $table->string('mobile');
            $table->text('address');
            $table->string('province');
            $table->string('city');
            $table->string('subdistrict');
            $table->string('village');
            $table->string('postal_code');
            $table->string('status')->nullable();
            $table->text('reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bo_infos');
    }
};
