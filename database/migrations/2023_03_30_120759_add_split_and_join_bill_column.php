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
        Schema::table('sales', function (Blueprint $table) {
            $table->unsignedBigInteger('sales_parent_id')->nullable();
            $table->foreign('sales_parent_id')->references('id')->on('sales');
            $table->enum('sales_type', ['single', 'join-child', 'join-parent', 'split-child', 'split-parent'])->default('single');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['sales_parent_id']);
            $table->dropColumn('sales_parent_id');
            $table->dropColumn('sales_type');
        });
    }
};
