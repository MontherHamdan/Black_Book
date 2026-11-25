<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::table('book_types', function (Blueprint $table) {
            $table->boolean('question_enabled')->default(false);
            $table->string('question_text', 500)->nullable();
            $table->boolean('question_required')->default(false);
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->string('product_question', 500)->nullable();
            $table->text('product_answer')->nullable();
        });
    }
    public function down(): void {
        Schema::table('orders', fn (Blueprint $table) => $table->dropColumn(['product_question', 'product_answer']));
        Schema::table('book_types', fn (Blueprint $table) => $table->dropColumn(['question_enabled', 'question_text', 'question_required']));
    }
};
