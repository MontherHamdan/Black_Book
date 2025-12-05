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
        Schema::table(
            'orders',
            function (Blueprint $table) {
                // لو العمود الجديد مش موجود → نضيفه
                if (! Schema::hasColumn('orders', 'transparent_printing_id')) {
                    $table->unsignedBigInteger('transparent_printing_id')
                        ->nullable()
                        ->after('additional_image_id');

                    $table->foreign('transparent_printing_id')
                        ->references('id')
                        ->on('user_images')
                        ->onDelete('set null');
                }

                // لو مش محتاج الأعمدة القديمة → نحذفها
                if (Schema::hasColumn('orders', 'transparent_printing_ids')) {
                    $table->dropColumn('transparent_printing_ids');
                }
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // رجوع للوضع القديم (اختياري)
            if (Schema::hasColumn('orders', 'transparent_printing_id')) {
                $table->dropForeign(['transparent_printing_id']);
                $table->dropColumn('transparent_printing_id');
            }

            if (! Schema::hasColumn('orders', 'transparent_printing_ids')) {
                $table->longText('transparent_printing_ids')->nullable();
            }
        });
    }
};
