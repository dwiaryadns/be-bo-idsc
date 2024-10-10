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
        Schema::create('history_legal_doc', function (Blueprint $table) {
            $table->id(); // Primary column (auto-incremented id)
            $table->unsignedBigInteger('legal_doc_bo_id'); // Foreign key to BoInfos table
            $table->string('status', 155); // Status with varchar type and length of 155
            $table->string('petugas', 255); // Petugas with varchar type and length of 255
            $table->timestamps(); // created_at and updated_at fields

            // Define foreign key constraints
            $table->foreign('legal_doc_bo_id')
                ->references('id') // Assuming 'id' is the primary key in BoInfos
                ->on('legal_doc_bo') // Referencing the BoInfos table
                ->onDelete('cascade'); // Handle delete case (optional)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('history_legal_doc');
    }
};
