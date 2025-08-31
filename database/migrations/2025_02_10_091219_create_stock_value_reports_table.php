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
        Schema::create('stock_value_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_category_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('outlet_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('inventory_id')->nullable()->constrained()->onDelete('cascade');
            $table->integer('initial_stock');
            $table->integer('purchases')->default(0);
            $table->integer('sales')->default(0);
            $table->integer('final_stock');
            $table->decimal('purchase_price', 10, 2);
            $table->decimal('stock_value', 10, 2);
            $table->decimal('selling_price', 10, 2);
            $table->decimal('potential_value', 10, 2);
            $table->date('expired_date')->nullable();
            $table->date('report_date');
            $table->foreignId('supplier_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_value_reports');
    }
};
