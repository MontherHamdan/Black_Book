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
        Schema::table('discount_codes', function (Blueprint $table) {
            $table->boolean('is_group')->default(false)->after('discount_type');
            $table->foreignId('plan_id')->nullable()->constrained('plans')->nullOnDelete()->after('is_group');

            $table->integer('discount_value')->nullable()->change();
        });
        DB::statement("ALTER TABLE discount_codes MODIFY discount_type ENUM('percentage', 'byJd') NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('discount_codes', function (Blueprint $table) {
            $table->dropForeign(['plan_id']);
            $table->dropColumn(['is_group', 'plan_id']);

            $table->integer('discount_value')->nullable(false)->change();
        });

        DB::statement("ALTER TABLE discount_codes MODIFY discount_type ENUM('percentage', 'byJd') NOT NULL DEFAULT 'percentage'");
    }
};
