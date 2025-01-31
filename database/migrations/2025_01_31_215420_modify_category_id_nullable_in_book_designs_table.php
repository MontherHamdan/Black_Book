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
        Schema::table('book_designs', function (Blueprint $table) {
            // Temporarily drop the foreign key constraint
            $table->dropForeign(['category_id']);

            // Modify the column to be nullable
            $table->unsignedBigInteger('category_id')->nullable()->change();

            // Re-add the foreign key constraint
            $table->foreign('category_id')
                  ->references('id')
                  ->on('book_design_categories')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('book_designs', function (Blueprint $table) {
            // Drop the existing foreign key constraint
            $table->dropForeign(['category_id']);

            // Modify the column to be not nullable again
            $table->foreignId('category_id')
                  ->constrained('book_design_categories')
                  ->onDelete('cascade')
                  ->change();
        });
    }
};
