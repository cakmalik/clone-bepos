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
        Schema::create('product_stock_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outlet_id')->nullable();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('inventory_id')->nullable()->constrained('inventories');
            $table->string('document_number')->nullable();
            $table->dateTime('history_date')->nullable();
            $table->bigInteger('stock_change');
            $table->bigInteger('stock_before');
            $table->bigInteger('stock_after');
            $table->enum('action_type', ['minus', 'plus']);
            $table->text('desc');
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
        Schema::dropIfExists('product_stock_histories');
    }
};
