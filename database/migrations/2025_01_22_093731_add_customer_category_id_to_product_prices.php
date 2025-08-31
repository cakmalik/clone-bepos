<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('product_prices', function (Blueprint $table) {
            $table->unsignedBigInteger('customer_category_id')->after('product_id')->nullable();
            $table->foreign('customer_category_id')->references('id')->on('customer_categories')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('product_prices', function (Blueprint $table) {
            $table->dropForeign(['customer_category_id']);
            $table->dropColumn('customer_category_id');
        });
    }
};
