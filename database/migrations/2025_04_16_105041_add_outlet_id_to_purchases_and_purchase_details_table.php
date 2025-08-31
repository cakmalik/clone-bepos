<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOutletIdToPurchasesAndPurchaseDetailsTable extends Migration
{
    public function up(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->unsignedBigInteger('outlet_id')->nullable()->after('inventory_id');

            // Jika pakai foreign key
            $table->foreign('outlet_id')->references('id')->on('outlets')->onDelete('set null');
        });

        Schema::table('purchase_details', function (Blueprint $table) {
            $table->unsignedBigInteger('outlet_id')->nullable()->after('inventory_id');

            // Jika pakai foreign key
            $table->foreign('outlet_id')->references('id')->on('outlets')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('purchase_details', function (Blueprint $table) {
            $table->dropForeign(['outlet_id']);
            $table->dropColumn('outlet_id');
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->dropForeign(['outlet_id']);
            $table->dropColumn('outlet_id');
        });
    }
}
