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
        Schema::create('purchase_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_id')->nullable();
            $table->foreignId('purchase_id')->constrained('purchases');
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('purchase_po_id')->nullable();
            $table->foreignId('purchase_receipt_id')->nullable();
            $table->string('code')->nullable();
            $table->string('product_code')->nullable();
            $table->string('product_name')->nullable();
            $table->bigInteger('qty')->nullable();
            $table->bigInteger('price')->nullable();
            $table->bigInteger('discount')->nullable();
            $table->bigInteger('final_price')->nullable();
            $table->bigInteger('subtotal')->nullable();
            $table->bigInteger('accepted_qty')->default(0)->nullable();
            $table->bigInteger('returned_qty')->default(0)->nullable();
            $table->enum('status', ['Purchase Requisition', 'Purchase Order', 'Material Receipt', 'Purchase Retur'])->nullable();
            $table->timestamp('deleted_at')->nullable();
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
        Schema::dropIfExists('purchase_details');
    }
};
