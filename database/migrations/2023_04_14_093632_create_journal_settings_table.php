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
        Schema::create('journal_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('debit_account_id')->nullable()->constrained('journal_accounts')->restrictOnDelete();
            $table->foreignId('credit_account_id')->nullable()->constrained('journal_accounts')->restrictOnDelete();
            $table->string('name');
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('journal_settings');
    }
};
