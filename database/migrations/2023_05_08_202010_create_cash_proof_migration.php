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
        Schema::create('cash_proofs', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->dateTime('date');
            $table->string('received_from');
            $table->enum('type', ['KAS-KELUAR', 'KAS-MASUK']);
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('cash_proof_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cash_proof_id')->constrained('cash_proofs')->cascadeOnDelete();
            $table->foreignId('cash_master_id')->constrained('cash_masters')->restrictOnDelete();
            $table->string('ref_code');
            $table->text('description')->nullable();
            $table->bigInteger('nominal');
            $table->softDeletes();
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
        Schema::dropIfExists('cash_proofs_items');
        Schema::dropIfExists('cash_proofs');
    }
};
