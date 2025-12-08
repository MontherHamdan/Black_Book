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
            $table->dropForeign('orders_additional_image_id_foreign');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->longText('additional_image_id')->nullable()->change();
        });

        DB::statement("
            UPDATE orders
            SET additional_image_id =
                CASE
                    WHEN additional_image_id IS NULL OR additional_image_id = '' THEN NULL
                    WHEN additional_image_id LIKE '[%' THEN additional_image_id
                    ELSE CONCAT('[', additional_image_id, ']')
                END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("
            UPDATE orders
            SET additional_image_id =
                CASE
                    WHEN additional_image_id IS NULL OR additional_image_id = '' THEN NULL
                    ELSE JSON_UNQUOTE(JSON_EXTRACT(additional_image_id, '$[0]'))
                END
        ");

        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('additional_image_id')->nullable()->change();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('additional_image_id')
                ->references('id')->on('user_images')
                ->nullOnDelete();
        });
    }
};
