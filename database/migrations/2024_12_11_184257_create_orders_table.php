<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->enum('user_gender', ['male', 'female', 'group']);
            $table->unsignedBigInteger('discount_code_id')->nullable();
            $table->unsignedBigInteger('book_type_id');
            $table->unsignedBigInteger('book_design_id');
            $table->unsignedBigInteger('front_image_id')->nullable();
            $table->json('back_image_ids')->nullable();
            $table->enum('user_type', ['university', 'diploma']);
            $table->string('username_ar');
            $table->string('username_en');
            $table->string('school_name');
            $table->string('major_name');
            $table->unsignedBigInteger('svg_id')->nullable();
            $table->string('svg_title')->nullable();
            $table->longText('note')->nullable();
            $table->string('user_phone_number');
            $table->boolean('is_sponge');
            $table->integer('pages_number');
            $table->boolean('book_accessory');
            $table->unsignedBigInteger('additional_image_id')->nullable();
            $table->unsignedBigInteger('transparent_printing_id')->nullable();
            $table->string('delivery_number_one');
            $table->string('delivery_number_two')->nullable();
            $table->longText('governorate');
            $table->text('address');
            $table->decimal('final_price', 10, 2);
            $table->decimal('final_price_with_discount', 10, 2);
            $table->string('status')->default('preparing')->nullable();

            // Foreign keys
            $table->foreign('discount_code_id')->references('id')->on('discount_codes')->onDelete('set null');
            $table->foreign('book_type_id')->references('id')->on('book_types');
            $table->foreign('book_design_id')->references('id')->on('book_designs');
            $table->foreign('front_image_id')->references('id')->on('user_images')->onDelete('set null');
            $table->foreign('additional_image_id')->references('id')->on('user_images')->onDelete('set null');
            $table->foreign('transparent_printing_id')->references('id')->on('user_images')->onDelete('set null');

            $table->timestamps(); // Created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
