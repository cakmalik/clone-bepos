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
        Schema::create('journal_numbers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_type_id')->constrained('journal_types')->restrictOnDelete();
            $table->foreignId('outlet_id')->nullable()->constrained('outlets')->restrictOnDelete();
            $table->foreignId('inventory_id')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->restrictOnDelete();
            $table->foreignId('journal_closing_id')->nullable()->constrained('journal_closings')->restrictOnDelete();
            $table->unsignedBigInteger('user_approved_id')->nullable();
            $table->foreign('user_approved_id')->references('id')->on('users')->restrictOnDelete();
            $table->string('code');
            $table->string('ref_code')->nullable();
            $table->datetime('date');
            $table->boolean('status_approved')->default(false);
            $table->boolean('is_done')->default(true);
            $table->text('description')->nullable();
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
        Schema::dropIfExists('journal_numbers');
    }
};
