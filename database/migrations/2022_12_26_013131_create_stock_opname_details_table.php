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
        Schema::create('stock_opname_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_opname_id')->constrained('stock_opnames');
            $table->foreignId('product_id')->constrained('products');
            $table->string('code');
            $table->string('ref_code')->nullable()->constrained('stock_opnames');
            $table->bigInteger('qty_system')->nullable();
            $table->bigInteger('qty_so')->nullable();
            $table->bigInteger('qty_selisih')->nullable();
            $table->bigInteger('qty_adjustment')->nullable();
            $table->bigInteger('qty_after_adjustment')->nullable();
            $table->bigInteger('adjustment_nominal_value')->nullable();
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
        Schema::dropIfExists('stock_opname_details');
    }
};
