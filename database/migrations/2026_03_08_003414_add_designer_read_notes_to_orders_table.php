<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('orders', 'designer_read_notes')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->boolean('designer_read_notes')->default(0)->after('notebook_followup_note');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('orders', 'designer_read_notes')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('designer_read_notes');
            });
        }
    }
};