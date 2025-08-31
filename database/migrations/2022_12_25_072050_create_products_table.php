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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outlet_id')->constrained('outlets');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('product_category_id')->constrained('product_categories');
            $table->foreignId('product_unit_id')->constrained('product_units');
            $table->string('code');
            $table->string('name');
            $table->string('slug')->nullable();
            $table->enum('type_product', ['product', 'material'])->nullable();
            $table->bigInteger('minimum_stock')->nullable();
            $table->bigInteger('capital_price')->nullable();
            $table->text('product_image')->nullable();
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
        Schema::dropIfExists('products');
    }
};
