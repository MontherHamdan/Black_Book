<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('diplomas', function (Blueprint $table) {
            $table->id();  // Creates an auto-incrementing ID column
            $table->string('name');  // Column for the diploma name
            $table->string('governorate_name')->nullable();  // Optional column for the governorate name
            $table->timestamps();  // Timestamp columns for created_at and updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('diplomas');  // Drops the diplomas table if the migration is rolled back
    }
};
