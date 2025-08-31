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
        Schema::create('journal_category_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_category_id');
            $table->foreignId('journal_setting_trans_id');
            $table->foreignId('journal_setting_buy_id');
            $table->foreignId('journal_setting_invoice_id');
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
        Schema::dropIfExists('journal_category_products');
    }
};
