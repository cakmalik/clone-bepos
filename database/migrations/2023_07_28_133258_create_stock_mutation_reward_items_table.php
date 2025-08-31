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
        Schema::create('stock_mutation_reward_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_mutation_reward_id');
            $table->foreignId('product_id');
            $table->foreignId('outlet_id');
            $table->datetime('date');
            $table->bigInteger('qty');
            $table->softDeletes();
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
        Schema::dropIfExists('stock_mutation_reward_items');
    }
};
