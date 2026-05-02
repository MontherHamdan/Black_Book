<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ✅ hasColumn خارج الـ closure — هاد هو الصح
        if (! Schema::hasColumn('orders', 'delivery_target')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->enum('delivery_target', ['home', 'university'])
                    ->nullable()
                    ->after('area_id');
            });
        }

        if (! Schema::hasColumn('orders', 'delivery_university_id')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->unsignedBigInteger('delivery_university_id')
                    ->nullable()
                    ->after('delivery_target');
            });
        }
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['delivery_target', 'delivery_university_id']);
        });
    }
};
