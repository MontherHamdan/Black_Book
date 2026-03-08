<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'dispatched_at')) {
                $table->timestamp('dispatched_at')->nullable()->after('status');
            }
        });

        // Ensure currently "out_for_delivery" orders don't skip the auto-complete logic
        // We initialize dispatched_at to their updated_at so they can properly catch the 24h cron
        DB::statement("UPDATE orders SET dispatched_at = updated_at WHERE status = 'out_for_delivery' AND dispatched_at IS NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'dispatched_at')) {
                $table->dropColumn('dispatched_at');
            }
        });
    }
};
