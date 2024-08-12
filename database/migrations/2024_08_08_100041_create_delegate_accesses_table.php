<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDelegateAccessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delegate_accesses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bisnis_owner_id')->constrained('bisnis_owners')->onDelete('cascade');
            $table->string('role');
            $table->string('name');
            $table->boolean('is_verif')->default(false);
            $table->boolean('is_active')->default(true);
            $table->string('email')->unique();
            $table->string('password');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('delegate_accesses');
    }
}
