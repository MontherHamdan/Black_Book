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
        if (! Schema::hasColumn('orders', 'decoration_note')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->string('decoration_note')->nullable()->after('book_decorations_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('orders', 'decoration_note')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('decoration_note');
            });
        }
    }
};
