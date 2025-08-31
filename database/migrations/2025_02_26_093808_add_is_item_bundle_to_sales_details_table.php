<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('sales_details', function (Blueprint $table) {
            $table->boolean('is_item_bundle')->default(false)->after('is_bundle');
            //parent_sales_detail_id
            $table->integer('parent_sales_detail_id')->nullable()->after('is_item_bundle');
        });
    }

    public function down()
    {
        Schema::table('sales_details', function (Blueprint $table) {
            $table->dropColumn('is_item_bundle');
            $table->dropColumn('parent_sales_detail_id');
        });
    }
};
