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
            $table->unsignedBigInteger('university_id')->nullable()->after('user_type');
            $table->unsignedBigInteger('university_major_id')->nullable()->after('university_id');
            $table->unsignedBigInteger('diploma_id')->nullable()->after('university_major_id');
            $table->unsignedBigInteger('diploma_major_id')->nullable()->after('diploma_id');

            $table->string('school_name')->nullable()->change();
            $table->string('major_name')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('school_name')->nullable(false)->change();
            $table->string('major_name')->nullable(false)->change();

            $table->dropColumn([
                'university_id',
                'university_major_id',
                'diploma_id',
                'diploma_major_id',
            ]);
        });
    }
};
