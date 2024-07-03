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
        Schema::create('master_kfa_poas', function (Blueprint $table) {
            $table->unsignedBigInteger('kfa_poa_code')->primary()->index();
            $table->unsignedBigInteger('kfa_pov_code');
            $table->foreign('kfa_pov_code')->references('kfa_pov_code')->on('master_kfa_povs')->onDelete('cascade');
            $table->string('kfa_poa_idsc');
            $table->text('poa_desc');
            $table->string('manufacture')->nullable();
            $table->boolean('generic_flag');
            $table->string('made_in');
            $table->string('kfa_code_poak')->nullable();
            $table->string('pack_type')->nullable();
            $table->decimal('estimate_pack_price', 15, 2)->nullable();
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_kfa_poas');
    }
};
