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
        Schema::table('sales_details', function (Blueprint $table) {
            $table->enum('process_status', ['waiting', 'inprogress', 'done', 'served', 'cancel'])->default('done')->after('profit');
            $table->foreignId('handler_user_id')->nullable()->constrained('users')->onDelete('restrict');
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->foreignId('creator_user_id')->constrained('users')->onDelete('restrict');
            $table->enum('process_status', ['waiting', 'inprogress', 'some', 'done', 'served', 'cancel', 'pause'])->default('done')->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales_details', function (Blueprint $table) {
            $table->dropForeign(['handler_user_id']);
            $table->dropColumn('process_status');
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['creator_user_id']);
            $table->dropColumn('process_status');
        });
    }
};
