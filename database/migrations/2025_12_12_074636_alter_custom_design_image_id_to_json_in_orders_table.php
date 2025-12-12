<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->json('custom_design_image_id')->nullable()->change();
        });

        DB::statement("
        UPDATE orders
        SET custom_design_image_id = JSON_ARRAY(custom_design_image_id)
        WHERE custom_design_image_id IS NOT NULL
          AND JSON_VALID(custom_design_image_id) = 0
    ");
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('custom_design_image_id')->nullable()->change();
        });
    }
};
