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
            $table->enum('gift_type', ['default', 'custom', 'none'])
                ->default('default')
                ->after('book_decorations_id'); 
        });
 
        DB::table('orders')
            ->whereNotNull('gift_title')
            ->update(['gift_type' => 'custom']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('gift_type');
        });
    }
};
