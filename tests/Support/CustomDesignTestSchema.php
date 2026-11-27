<?php

namespace Tests\Support;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

trait CustomDesignTestSchema
{
    private function createDesignTestSchema(): void
    {
        // Legacy migrations use MySQL-only foreign-key alterations. Build the
        // existing order/catalog contract in isolated SQLite, then run the real new migration.
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            foreach (['book_type_id', 'book_design_id', 'book_decorations_id', 'university_id', 'university_major_id', 'delivery_university_id', 'designer_id'] as $field) {
                $table->unsignedBigInteger($field)->nullable();
            }
            foreach (['user_gender', 'user_type', 'username_ar', 'username_en', 'user_phone_number', 'delivery_number_one', 'delivery_target', 'gift_type', 'gift_title', 'status'] as $field) {
                $table->string($field)->nullable();
            }
            $table->json('back_image_ids')->nullable();
            $table->json('custom_design_image_id')->nullable();
            $table->boolean('is_sponge')->default(false);
            $table->boolean('is_with_additives')->default(false);
            $table->integer('pages_number')->nullable();
            $table->decimal('final_price', 10, 2)->nullable();
            $table->decimal('final_price_with_discount', 10, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('book_types', function (Blueprint $table) {
            $table->id();
            $table->decimal('price', 10, 2);
            $table->string('name_ar')->nullable();
            $table->string('name_en')->nullable();
            $table->string('image')->nullable();
            $table->text('description_ar')->nullable();
            $table->text('description_en')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        foreach (['book_designs', 'book_decorations', 'universities'] as $name) {
            Schema::create($name, fn (Blueprint $table) => $table->id());
            DB::table($name)->insert(['id' => 1]);
        }
        Schema::create('majors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('university_id');
        });
        Schema::create('page_options', function (Blueprint $table) {
            $table->id();
            $table->integer('pages');
            $table->decimal('extra_price', 10, 2)->default(0);
            $table->softDeletes();
        });
        Schema::create('svg_names', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('normalized_name')->unique();
            $table->timestamps();
        });
        DB::table('book_types')->insert(['id' => 1, 'price' => 13]);
        DB::table('majors')->insert(['id' => 1, 'university_id' => 1]);
        (require database_path('migrations/2026_11_25_000000_create_custom_design_uploads.php'))->up();
        (require database_path('migrations/2026_11_25_010000_add_product_questions.php'))->up();
        (require database_path('migrations/2026_11_27_204500_add_decoration_note_to_orders_table.php'))->up();
    }
}
