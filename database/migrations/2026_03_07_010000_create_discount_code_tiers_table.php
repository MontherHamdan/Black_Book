<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('discount_code_tiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('discount_code_id')
                ->constrained('discount_codes')
                ->cascadeOnDelete();
            $table->unsignedInteger('min_qty');
            $table->decimal('discount_value', 8, 2);
            $table->enum('discount_type', ['percentage', 'byJd']);
            $table->timestamps();

            $table->index(['discount_code_id', 'min_qty']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discount_code_tiers');
    }
};
