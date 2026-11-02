<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('page_options', function (Blueprint $table) {
            $table->id();
            $table->integer('pages')->unique();
            $table->string('label');
            $table->decimal('extra_price', 8, 2)->default(0.00);
            $table->boolean('is_default')->default(false);
            $table->string('badge')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });

        // Insert initial default page options
        $now = now();
        DB::table('page_options')->insert([
            [
                'pages' => 100,
                'label' => '100 ورقة',
                'extra_price' => 0.00,
                'is_default' => false,
                'badge' => null,
                'is_active' => true,
                'sort_order' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'pages' => 150,
                'label' => '150 ورقة',
                'extra_price' => 0.00,
                'is_default' => true,
                'badge' => 'الأكثر طلباً 🔥',
                'is_active' => true,
                'sort_order' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'pages' => 200,
                'label' => '200 ورقة',
                'extra_price' => 0.00,
                'is_default' => false,
                'badge' => null,
                'is_active' => true,
                'sort_order' => 3,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_options');
    }
};
