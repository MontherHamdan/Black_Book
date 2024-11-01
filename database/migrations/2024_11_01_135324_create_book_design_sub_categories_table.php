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
        Schema::create('book_designs', function (Blueprint $table) {
            $table->id();
            $table->string('image');
            $table->foreignId('category_id')->constrained('book_design_categories')->onDelete('cascade');
            $table->foreignId('sub_category_id')->nullable()->constrained('book_design_sub_categories')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_designs');
    }
};
