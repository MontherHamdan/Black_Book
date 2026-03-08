<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // نفحص أولاً: إذا العمود مش موجود، ضيفه
        if (!Schema::hasColumn('orders', 'notebook_followup_note')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->text('notebook_followup_note')->nullable()->after('binding_followup_note');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('orders', 'notebook_followup_note')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('notebook_followup_note');
            });
        }
    }
};