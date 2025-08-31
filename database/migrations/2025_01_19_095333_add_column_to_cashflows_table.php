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
        Schema::table('cashflows', function (Blueprint $table) {
            $table->dateTime('transaction_date')->nullable()->after('transaction_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cashflows', function (Blueprint $table) {
            $table->dropColumn('transaction_date');
        });
    }
};
