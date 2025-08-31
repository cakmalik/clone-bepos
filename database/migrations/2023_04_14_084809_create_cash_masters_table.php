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
        Schema::create('cash_masters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_setting_id');
            $table->string('code');
            $table->string('name');
            $table->enum('cash_type', ['KAS-MASUK', 'KAS-KELUAR']);
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
        Schema::dropIfExists('cash_masters');
    }
};
