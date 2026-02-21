<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('base_order_price', 8, 2)->default(0)->after('role')->comment('السعر الأساسي للطلب');
            $table->decimal('decoration_price', 8, 2)->default(0)->after('base_order_price')->comment('سعر صورة الزخرفة');
            $table->decimal('custom_gift_price', 8, 2)->default(0)->after('decoration_price')->comment('سعر الإهداء المخصص');
            $table->decimal('internal_image_price', 8, 2)->default(0)->after('custom_gift_price')->comment('سعر الصورة الداخلية');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'base_order_price',
                'decoration_price',
                'custom_gift_price',
                'internal_image_price'
            ]);
        });
    }
};
