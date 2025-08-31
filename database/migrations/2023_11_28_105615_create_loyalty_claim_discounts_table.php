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
        Schema::create('loyalty_claim_discounts', function (Blueprint $table) {
            $table->id();
            $table->integer('previous_score');
            $table->integer('value');
            $table->string('type')->default('discount');
            $table->enum('discount_type', ['PERCENT', 'NOMINAL']);
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
        Schema::dropIfExists('loyalty_claim_discounts');
    }
};
