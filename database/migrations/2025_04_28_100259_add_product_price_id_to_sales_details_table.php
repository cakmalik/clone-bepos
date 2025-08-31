<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales_details', function (Blueprint $table) {
            $table->unsignedBigInteger('product_price_id')->nullable()->after('product_id');
        });
    }

    public function down(): void
    {
        Schema::table('sales_details', function (Blueprint $table) {
            $table->dropColumn('product_price_id');
        });
    }
};
