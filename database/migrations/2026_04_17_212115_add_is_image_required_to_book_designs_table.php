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
            $table->boolean('is_image_required')->default(false)->after('sub_category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('book_designs', function (Blueprint $table) {
            $table->dropColumn('is_image_required');
        });
    }
};
