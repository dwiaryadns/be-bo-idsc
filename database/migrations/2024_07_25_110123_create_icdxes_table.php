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
        Schema::create('icdxes', function (Blueprint $table) {
            $table->id()->index();
            $table->string('category');
            $table->string('sub_category')->nullable();
            $table->string('en_name');
            $table->string('id_name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('icdxes');
    }
};
