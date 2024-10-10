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
        Schema::create('history_bo_info', function (Blueprint $table) {
            $table->id(); // Primary column (auto-incremented id)
            $table->unsignedBigInteger('bo_info_id'); // Foreign key to BoInfos table
            $table->string('status', 155); // Status with varchar type and length of 155
            $table->string('petugas', 255); // Petugas with varchar type and length of 255
            $table->timestamp('created_at')->useCurrent(); // Timestamp with CURRENT_TIMESTAMP default
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Timestamp with onUpdate behavior

            $table->foreign('bo_info_id')
                ->references('id') // Assuming 'id' is the primary key in BoInfos
                ->on('bo_infos') // Referencing the BoInfos table
                ->onDelete('cascade'); // Handle cascading delete (optional)

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('history_bo_info');
    }
};
