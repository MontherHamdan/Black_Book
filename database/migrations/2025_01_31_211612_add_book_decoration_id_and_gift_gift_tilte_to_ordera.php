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
            $table->unsignedBigInteger('book_decorations_id')->nullable();
            $table->longText('gift_title');

            // foreign keys
            $table->foreign('book_decorations_id')->references('id')->on('book_decorations')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['book_decorations_id']);

            // Drop the columns
            $table->dropColumn(['book_decorations_id', 'gift_title']);
        });
    }
};
