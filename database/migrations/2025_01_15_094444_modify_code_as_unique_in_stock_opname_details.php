<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up()
    {
        Schema::table('stock_opname_details', function (Blueprint $table) {
            // Periksa apakah constraint sudah ada, jika belum tambahkan.
            if (!Schema::hasColumn('stock_opname_details', 'code')) {
                return; // Berhenti jika kolom tidak ditemukan
            }

            // Pastikan semua kode unik sebelum menambahkan constraint
            DB::statement("
            UPDATE stock_opname_details t1
            JOIN (
                SELECT id, CONCAT(code, '-', ROW_NUMBER() OVER (PARTITION BY code ORDER BY id)) as new_code
                FROM stock_opname_details
                WHERE code IN (
                    SELECT code
                    FROM stock_opname_details
                    GROUP BY code
                    HAVING COUNT(*) > 1
                )
            ) t2 ON t1.id = t2.id
            SET t1.code = t2.new_code;
        ");

            $table->unique('code');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stock_opname_details', function (Blueprint $table) {
            $table->dropUnique('stock_opname_details_code_unique');
        });
    }
};
