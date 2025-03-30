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
        Schema::table('orders', function (Blueprint $table) {
            // First drop the foreign key constraint
            $table->dropForeign(['transparent_printing_id']);
            
            // Then drop the existing column
            $table->dropColumn('transparent_printing_id');
            
            // Add the new column as JSON type to store multiple IDs
            $table->json('transparent_printing_ids')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Drop the new JSON column
            $table->dropColumn('transparent_printing_ids');
            
            // Recreate the original column
            $table->unsignedBigInteger('transparent_printing_id')->nullable();
            
            // Recreate the foreign key constraint
            $table->foreign('transparent_printing_id')->references('id')->on('user_images')->onDelete('set null');
        });
    }
};
