<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('flag_image');
        });

        Schema::table('governorates', function (Blueprint $table) {
            if (!Schema::hasColumn('governorates', 'country_id')) {
                $table->foreignId('country_id')->nullable()->after('id')->constrained('countries')->cascadeOnDelete();
            }
            $table->boolean('is_active')->default(true)->after('logestechs_id');
        });

        Schema::table('cities', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('logestechs_id');
        });

        Schema::table('areas', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('logestechs_id');
        });
    }

    public function down()
    {
        Schema::table('countries', function (Blueprint $table) { $table->dropColumn('is_active'); });
        Schema::table('governorates', function (Blueprint $table) { $table->dropForeign(['country_id']); $table->dropColumn(['country_id', 'is_active']); });
        Schema::table('cities', function (Blueprint $table) { $table->dropColumn('is_active'); });
        Schema::table('areas', function (Blueprint $table) { $table->dropColumn('is_active'); });
    }
};