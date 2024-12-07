<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('diploma_majors', function (Blueprint $table) {
            $table->id();  // Auto-incrementing ID column
            $table->unsignedBigInteger('diploma_id');  // Foreign key to the diplomas table
            $table->string('name');  // Major name
            $table->timestamps();  // Timestamp columns for created_at and updated_at

            // Add the foreign key constraint
            $table->foreign('diploma_id')->references('id')->on('diplomas')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('diploma_majors');  // Drops the diploma_majors table if rolled back
    }
};
