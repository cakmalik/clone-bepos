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
        Schema::create('journal_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_account_type_id')->constrained('journal_account_types');
            $table->string('code');
            $table->string('name');
            // $table->enum('position', ['neraca', 'laba rugi']);
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
        Schema::dropIfExists('journal_accounts');
    }
};
