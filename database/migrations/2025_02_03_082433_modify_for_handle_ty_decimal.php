<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('capital_price', 15, 2)->change();
            $table->decimal('minimum_stock', 15, 2)->change();
            $table->foreignId('unit_id')->nullable()->constrained('product_units');
            $table->boolean('is_support_qty_decimal')->default(false);
        });

        Schema::table('product_units', function (Blueprint $table) {
            $table->unsignedBigInteger('base_unit_id')->nullable()->after('id');
            $table->decimal('conversion_rate', 15, 6)->nullable()->after('base_unit_id');
            $table->foreign('base_unit_id')->references('id')->on('product_units')->onDelete('set null');
            $table->string('symbol')->nullable()->after('base_unit_id');
        });

        Schema::table('cashflows', function (Blueprint $table) {
            $table->decimal('amount', 15, 2)->change();
            $table->decimal('total_hpp', 15, 2)->change();
            $table->decimal('profit', 15, 2)->nullable()->change();
        });

        Schema::table('cashflow_close', function (Blueprint $table) {
            $table->decimal('capital_amount', 15, 2)->change();
            $table->decimal('income_amount', 15, 2)->change();
            $table->decimal('expense_amount', 15, 2)->change();
            $table->decimal('profit_amount', 15, 2)->change();
            $table->decimal('difference', 15, 2)->change();
            $table->decimal('real_amount', 15, 2)->change();
        });

        Schema::table('product_prices', function (Blueprint $table) {
            $table->decimal('price', 15, 2)->change();
        });

        Schema::table('product_stocks', function (Blueprint $table) {
            $table->decimal('stock_current', 15, 2)->change();
        });

        Schema::table('product_stock_histories', function (Blueprint $table) {
            $table->decimal('stock_change', 15, 2)->change();
            $table->decimal('stock_before', 15, 2)->change();
            $table->decimal('stock_after', 15, 2)->change();
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->decimal('nominal_amount', 15, 2)->change();
            $table->decimal('discount', 15, 2)->change();
            $table->decimal('final_amount', 15, 2)->change();
        });

        Schema::table('purchase_details', function (Blueprint $table) {
            $table->decimal('qty', 15, 2)->change();
            $table->decimal('price', 15, 2)->change();
            $table->decimal('discount', 15, 2)->change();
            $table->decimal('final_price', 15, 2)->change();
            $table->decimal('subtotal', 15, 2)->change();
            $table->decimal('accepted_qty', 15, 2)->change();
            $table->decimal('returned_qty', 15, 2)->change();
        });

        Schema::table('purchase_invoices', function (Blueprint $table) {
            $table->decimal('nominal', 15, 2)->change();
            $table->decimal('nominal_returned', 15, 2)->change();
            $table->decimal('nominal_paid', 15, 2)->change();
        });

        Schema::table('receipt_details', function (Blueprint $table) {
            $table->decimal('accepted_qty', 15, 2)->change();
        });

        Schema::table('sales_details', function (Blueprint $table) {
            $table->decimal('qty', 12, 4)->change();
            $table->decimal('price', 12, 2)->change();
            $table->decimal('final_price', 12, 2)->change();
            $table->decimal('subtotal', 12, 2)->change();
            $table->decimal('hpp', 12, 2)->change();
            $table->decimal('subtotal', 12, 2)->change();
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->bigInteger('capital_price')->change();
            $table->bigInteger('minimum_stock')->change();
        });

        Schema::table('product_units', function (Blueprint $table) {
            $table->dropForeign(['base_unit_id']);
            $table->dropColumn(['base_unit_id', 'conversion_value']);
        });

        Schema::table('cashflows', function (Blueprint $table) {
            $table->bigInteger('amount')->change();
            $table->bigInteger('total_hpp')->change();
            $table->bigInteger('profit')->nullable()->change();
        });

        Schema::table('cashflow_close', function (Blueprint $table) {
            $table->bigInteger('capital_amount')->change();
            $table->bigInteger('income_amount')->change();
            $table->bigInteger('expense_amount')->change();
            $table->bigInteger('profit_amount')->change();
            $table->bigInteger('difference')->change();
            $table->bigInteger('real_amount')->change();
        });

        Schema::table('product_prices', function (Blueprint $table) {
            $table->bigInteger('price')->change();
        });

        Schema::table('product_stocks', function (Blueprint $table) {
            $table->bigInteger('stock_current')->change();
        });

        Schema::table('product_stock_histories', function (Blueprint $table) {
            $table->bigInteger('stock_change')->change();
            $table->bigInteger('stock_before')->change();
            $table->bigInteger('stock_after')->change();
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->bigInteger('nominal_amount')->change();
            $table->bigInteger('discount')->change();
            $table->bigInteger('final_amount')->change();
        });

        Schema::table('purchase_details', function (Blueprint $table) {
            $table->bigInteger('qty')->change();
            $table->bigInteger('price')->change();
            $table->bigInteger('discount')->change();
            $table->bigInteger('final_price')->change();
            $table->bigInteger('subtotal')->change();
            $table->bigInteger('accepted_qty')->change();
            $table->bigInteger('returned_qty')->change();
        });

        Schema::table('purchase_invoices', function (Blueprint $table) {
            $table->bigInteger('nominal')->change();
            $table->bigInteger('nominal_returned')->change();
            $table->bigInteger('nominal_paid')->change();
        });

        Schema::table('receipt_details', function (Blueprint $table) {
            $table->bigInteger('accepted_qty')->change();
        });

        Schema::table('sales_details', function (Blueprint $table) {
            $table->bigInteger('qty')->change();
            $table->bigInteger('price')->change();
            $table->bigInteger('final_price')->change();
            $table->bigInteger('subtotal')->change();
            $table->bigInteger('hpp')->change();
            $table->bigInteger('subtotal')->change();
        });
    }
};
