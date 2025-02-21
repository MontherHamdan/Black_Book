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
            $table->boolean('is_with_additives')->default(false); // Add new column
            $table->string('status')->default('Pending')->change(); // Change default value

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('is_with_additives'); // Remove column on rollback
            $table->string('status')->default('preparing')->change(); // Revert if rolled back

        });
    }
};
