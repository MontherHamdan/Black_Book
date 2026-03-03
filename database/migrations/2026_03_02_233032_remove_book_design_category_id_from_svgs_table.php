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
        Schema::table('svgs', function (Blueprint $table) {
            if (Schema::hasColumn('svgs', 'book_design_category_id')) {

                try {
                    $table->dropForeign(['book_design_category_id']);
                } catch (\Exception $e) {
                }

                $table->dropColumn('book_design_category_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('svgs', function (Blueprint $table) {
            $table->foreignId('book_design_category_id')
                ->nullable()
                ->constrained('book_design_categories')
                ->nullOnDelete();
        });
    }
};
