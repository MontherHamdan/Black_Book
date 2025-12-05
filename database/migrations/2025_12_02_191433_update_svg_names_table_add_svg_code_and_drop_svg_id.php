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
        Schema::table('svg_names', function (Blueprint $table) {
            $table->longText('svg_code')
                ->nullable()
                ->after('normalized_name');

            $table->dropForeign(['svg_id']);  
            $table->dropColumn('svg_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('svg_names', function (Blueprint $table) {
            $table->foreignId('svg_id')
                ->nullable()
                ->constrained('svgs')
                ->nullOnDelete()
                ->after('normalized_name');

            $table->dropColumn('svg_code');
        });
    }
};
