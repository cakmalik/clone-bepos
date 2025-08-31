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
        Schema::create('receipt_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_detail_id')->constrained('purchase_details');
            $table->string('code')->nullable();
            $table->string('received_ref_code')->nullable();
            $table->dateTime('received_date')->nullable();
            $table->string('shipment_ref_code')->nullable();
            $table->bigInteger('accepted_qty')->nullable();
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
        Schema::dropIfExists('receipt_details');
    }
};
