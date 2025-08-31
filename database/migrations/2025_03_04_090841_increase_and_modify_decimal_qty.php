<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('product_stocks', function (Blueprint $table) {
            $table->decimal('stock_current', 15, 10)->change();
        });

        Schema::table('product_stock_histories', function (Blueprint $table) {
            $table->decimal('stock_change', 15, 10)->change();
            $table->decimal('stock_before', 15, 10)->change();
            $table->decimal('stock_after', 15, 10)->change();
        });


        Schema::table('purchase_details', function (Blueprint $table) {
            $table->decimal('qty', 15, 10)->change();
            $table->decimal('accepted_qty', 15, 10)->change();
            $table->decimal('returned_qty', 15, 10)->change();
        });

        Schema::table('receipt_details', function (Blueprint $table) {
            $table->decimal('accepted_qty', 15, 10)->change();
        });

        Schema::table('sales_details', function (Blueprint $table) {
            $table->decimal('qty', 12, 10)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
