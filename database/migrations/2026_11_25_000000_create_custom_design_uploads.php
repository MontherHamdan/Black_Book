<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_design_uploads', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('purpose', 20);
            $table->string('token_hash', 64);
            $table->string('path');
            $table->string('original_name');
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('size');
            $table->timestamp('claimed_at')->nullable();
            $table->timestamps();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignUuid('custom_decoration_upload_id')->nullable()->unique()
                ->constrained('custom_design_uploads')->restrictOnDelete();
            $table->foreignUuid('custom_gift_upload_id')->nullable()->unique()
                ->constrained('custom_design_uploads')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['custom_decoration_upload_id']);
            $table->dropForeign(['custom_gift_upload_id']);
            $table->dropUnique(['custom_decoration_upload_id']);
            $table->dropUnique(['custom_gift_upload_id']);
            $table->dropColumn(['custom_decoration_upload_id', 'custom_gift_upload_id']);
        });
        Schema::dropIfExists('custom_design_uploads');
    }
};
