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

            // 🔥 نحذف الـ FK لو كان موجود (بدون ما يعمل Error لو ناقص)
            if (Schema::hasColumn('orders', 'internal_image_id')) {
                try {
                    $table->dropForeign(['internal_image_id']);
                } catch (\Exception $e) {
                    // تجاهل الخطأ: أحياناً يكون ما في FK
                }

                $table->dropColumn('internal_image_id');
            }

            if (Schema::hasColumn('orders', 'additional_image_ids')) {
                $table->dropColumn('additional_image_ids');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {

            // 🔄 نرجّع الأعمدة لو عملت rollback
            if (!Schema::hasColumn('orders', 'internal_image_id')) {
                $table->unsignedBigInteger('internal_image_id')->nullable()->after('front_image_id');
            }

            if (!Schema::hasColumn('orders', 'additional_image_ids')) {
                $table->longText('additional_image_ids')->nullable()->after('internal_image_id');
            }
        });
    }
};
