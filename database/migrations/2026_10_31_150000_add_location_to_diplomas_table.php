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
        Schema::table('diplomas', function (Blueprint $table) {
            $table->foreignId('governorate_id')->nullable()->after('governorate_name')->constrained('governorates')->nullOnDelete();
            $table->foreignId('city_id')->nullable()->after('governorate_id')->constrained('cities')->nullOnDelete();
            $table->foreignId('area_id')->nullable()->after('city_id')->constrained('areas')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('diplomas', function (Blueprint $table) {
            $table->dropForeign(['governorate_id']);
            $table->dropForeign(['city_id']);
            $table->dropForeign(['area_id']);
            $table->dropColumn(['governorate_id', 'city_id', 'area_id']);
        });
    }
};
