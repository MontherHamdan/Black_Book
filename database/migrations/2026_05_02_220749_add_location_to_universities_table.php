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
        Schema::table('universities', function (Blueprint $table) {
            $table->foreignId('governorate_id')->nullable()->constrained('governorates');
            $table->foreignId('city_id')->nullable()->constrained('cities');
            $table->foreignId('area_id')->nullable()->constrained('areas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('universities', function (Blueprint $table) {
            //
        });
    }
};
