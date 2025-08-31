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
        Schema::table('stock_mutations', function (Blueprint $table) {
            $table->unsignedBigInteger('outlet_source_id')->nullable()->after('inventory_destination_id');
            $table->foreign('outlet_source_id')
                ->references('id')
                ->on('outlets')
                ->onDelete('cascade');

            $table->unsignedBigInteger('outlet_destination_id')->nullable()->after('outlet_source_id');
            $table->foreign('outlet_destination_id')
                ->references('id')
                ->on('outlets')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stock_mutations', function (Blueprint $table) {
            $table->dropForeign(['outlet_source_id']);
            $table->dropColumn('outlet_source_id');
            $table->dropColumn('mutation_category');
        });
    }
};
