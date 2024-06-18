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
            $table->bigInteger('fasyankesId');
            $table->string('type');
            $table->string('sector');
            $table->string('duration');
            $table->string('package_plan');
            $table->foreignId('warehouse_id')->constrained()->onDelete('cascade');
            $table->foreignId('bisnis_owner_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('address');
            $table->string('pic');
            $table->string('pic_number');
            $table->string('email')->unique();
            $table->string('password');
            $table->timestamp('start_date');
            $table->timestamp('end_date');
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
