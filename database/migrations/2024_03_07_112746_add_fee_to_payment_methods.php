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
        if(!Schema::hasColumn('payment_methods', 'transaction_fees')){
            Schema::table('payment_methods', function (Blueprint $table) {
                $table->integer('transaction_fees')->default(0)->after('name');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if(Schema::hasColumn('payment_methods', 'transaction_fees')){
            Schema::table('payment_methods', function (Blueprint $table) {
                $table->dropColumn('transaction_fees');
            });
        }
    }
};
