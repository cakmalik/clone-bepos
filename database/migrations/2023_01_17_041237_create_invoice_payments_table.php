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
        Schema::create('invoice_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_invoice_id')->constrained('purchase_invoices');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('journal_number_id')->nullable();
            $table->string('code')->nullable();
            $table->bigInteger('nominal_payment')->nullable();
            $table->dateTime('payment_date')->nullable();
            $table->enum('payment_type', ['CASH', 'TRANSFER'])->nullable();
            $table->text('description')->nullable();
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
        Schema::dropIfExists('invoice_payments');
    }
};
