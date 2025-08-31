<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use SebastianBergmann\CliParser\AmbiguousOptionException;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cashflows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outlet_id')->constrained('outlets');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('cashflow_close_id')->nullable();
            $table->string('code');
            $table->string('transaction_code')->nullable();
            $table->enum('type', ['in', 'out', 'modal'])->default('in');
            $table->bigInteger('amount');
            $table->bigInteger('profit')->nullable();
            $table->string('desc');
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
        Schema::dropIfExists('cashflows');
    }
};
