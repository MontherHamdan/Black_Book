<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('is_design_downloaded')->default(false)->after('designer_gift_file');
            $table->boolean('is_internal_downloaded')->default(false)->after('is_design_downloaded');
            $table->boolean('is_decoration_downloaded')->default(false)->after('is_internal_downloaded');
            $table->boolean('is_gift_downloaded')->default(false)->after('is_decoration_downloaded');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'is_design_downloaded',
                'is_internal_downloaded',
                'is_decoration_downloaded',
                'is_gift_downloaded',
            ]);
        });
    }
};
