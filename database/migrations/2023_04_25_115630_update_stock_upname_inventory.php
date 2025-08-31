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
        Schema::table('stock_opnames', function (Blueprint $table) {
            $table->foreignId('inventory_id')->nullable()->after('id')->constrained('inventories')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stock_opnames', function (Blueprint $table) {
            $table->foreignId('outlet_id')->constrained('outlets');
            $table->dropForeign('inventory_id');
        });
    }
};
