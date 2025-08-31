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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers');
            $table->foreignId('inventory_id');
            $table->foreignId('purchase_invoice_id')->nullable();
            $table->foreignId('journal_number_id')->nullable();
            $table->foreignId('user_id')->constrained('users');
            $table->string('code');
            $table->string('ref_code')->nullable();
            $table->string('name')->nullable();
            $table->dateTime('purchase_date');
            $table->bigInteger('nominal_amount')->nullable();
            $table->bigInteger('discount')->nullable();
            $table->bigInteger('final_amount')->nullable();
            $table->enum('purchase_status', ['Draft', 'Finish', 'Void', 'Open']);
            $table->enum('purchase_type', ['Purchase Requisition', 'Purchase Order', 'Reception', 'Purchase Retur']);
            $table->string('desc')->nullable();
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
        Schema::dropIfExists('purchases');
    }
};
