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
        if (! Schema::hasColumn('orders', 'additional_images_placement')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->string('additional_images_placement')->nullable()->after('additional_image_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('orders', 'additional_images_placement')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('additional_images_placement');
            });
        }
    }
};
