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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('designer_design_file')->nullable();
            $table->string('designer_decoration_file')->nullable();
            $table->json('designer_internal_files')->nullable();
            $table->string('designer_gift_file')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['designer_design_file', 'designer_decoration_file', 'designer_internal_files', 'designer_gift_file']);
        });
    }
};
