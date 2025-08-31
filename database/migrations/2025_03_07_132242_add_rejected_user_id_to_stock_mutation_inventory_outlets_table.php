<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('stock_mutation_inventory_outlets', function (Blueprint $table) {
            $table->foreignId('rejected_user_id')->nullable()->after('creator_user_id')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('stock_mutation_inventory_outlets', function (Blueprint $table) {
            $table->dropForeign(['rejected_user_id']);
            $table->dropColumn('rejected_user_id');
        });
    }
};
