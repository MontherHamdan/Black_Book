<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // وجهة التوصيل: بيت أو جامعة
            $table->enum('delivery_target', ['home', 'university'])->nullable()->after('area_id');
            // جامعة التوصيل (تختلف عن university_id الخاص بمعلومات الخريج)
            $table->unsignedBigInteger('delivery_university_id')->nullable()->after('delivery_target');

            $table->foreign('delivery_university_id')
                  ->references('id')
                  ->on('universities')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['delivery_university_id']);
            $table->dropColumn(['delivery_target', 'delivery_university_id']);
        });
    }
};
