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
        Schema::create('bisnis_owners', function (Blueprint $table) {
            $table->id('id');
            $table->string('name');
            $table->string('phone');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->boolean('is_send_email')->default(0);
            $table->boolean('is_resend')->default(0);
            $table->boolean('is_first_login')->default(0);
            $table->string('img_profile')->nullable();
            $table->string('role')->nullable();
            $table->boolean('is_2fa')->default(0);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bisnis_owners');
    }
};
