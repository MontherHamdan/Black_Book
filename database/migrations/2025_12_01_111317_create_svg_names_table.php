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
        Schema::create('svg_names', function (Blueprint $table) {
            $table->id();

            $table->string('name');

            $table->string('normalized_name')->index();

            $table->foreignId('svg_id')
                ->nullable()
                ->constrained('svgs')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('svg_names');
    }
};
