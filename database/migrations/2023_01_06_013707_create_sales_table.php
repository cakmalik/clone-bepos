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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outlet_id')->constrained('outlets');
            $table->foreignId('cashier_machine_id')->constrained('cashier_machines')->nullable();
            $table->foreignId('user_id')->constrained('users');
            // $table->foreignId('customer_id')->nullable()->references('id')->on('customers');
            $table->foreignId('payment_method_id')->constrained('payment_methods')->nullable();
            $table->foreign('customer_id')->references('id')->on('customers')->nullable();
            $table->foreignId('journal_number_id')->nullable();
            $table->string('sale_code');
            $table->string('ref_code')->nullable();
            $table->bigInteger('customer_id')->nullable()->unsigned();
            $table->dateTime('sale_date');
            $table->bigInteger('nominal_amount');
            $table->bigInteger('discount_amount')->default(0);
            $table->string('discount_type')->default('nominal');
            $table->bigInteger('final_amount');
            $table->bigInteger('nominal_pay');
            $table->bigInteger('nominal_change');
            $table->boolean('is_retur')->default(false);
            $table->string('status');
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
        Schema::dropIfExists('sales');
    }
};
