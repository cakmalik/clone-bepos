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
        Schema::create('journal_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->foreignId('journal_number_id')->constrained('journal_numbers')->cascadeOnDelete();
            $table->foreignId('journal_account_id')->constrained('journal_accounts')->restrictOnDelete();
            $table->enum('type', ['debit', 'credit']);
            $table->bigInteger('nominal');
            $table->text('description')->nullable();
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
        Schema::dropIfExists('journal_transactions');
    }
};
