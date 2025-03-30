<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('book_accessory');
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('book_accessory')->after('pages_number');
        });
    }
};