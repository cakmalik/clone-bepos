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
        Schema::create('cashflow_close', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outlet_id')->constrained('outlets');
            $table->foreignId('user_id')->constrained('users');
            $table->bigInteger('capital_amount');
            $table->bigInteger('income_amount');
            $table->bigInteger('expense_amount');
            $table->datetime('date');
            $table->bigInteger('profit_amount');
            $table->bigInteger('difference');
            $table->bigInteger('real_amount');
            $table->string('close_type');
            $table->timestamp('deleted_at')->nullable();
            $table->text('desc')->nullable();
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
        Schema::dropIfExists('cashflow_closes');
    }
};
