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
        Schema::create('stock_mutations', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->datetime('date');
            $table->unsignedBigInteger('inventory_source_id');
            $table->foreign('inventory_source_id')->references('id')->on('inventories')->onDelete('restrict');
            $table->unsignedBigInteger('inventory_destination_id');
            $table->foreign('inventory_destination_id')->references('id')->on('inventories')->onDelete('restrict');
            $table->enum('status', ['draft', 'open', 'done', 'void']);
            $table->unsignedBigInteger('approved_user_id')->nullable();
            $table->foreign('approved_user_id')->references('id')->on('users');
            $table->unsignedBigInteger('received_user_id')->nullable();
            $table->foreign('received_user_id')->references('id')->on('users');
            $table->unsignedBigInteger('creator_user_id')->nullable();
            $table->foreign('creator_user_id')->references('id')->on('users');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('stock_mutation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_mutation_id')->constrained('stock_mutations')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('restrict');
            $table->integer('qty')->default(0);
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
        Schema::dropIfExists('stock_mutations');
        Schema::dropIfExists('stock_mutation_items');
    }
};
