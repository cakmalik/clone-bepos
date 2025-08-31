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
        Schema::create('outlets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outlet_parent_id')->nullable();
            $table->enum('type', ['resto', 'minimarket'])->default('minimarket');
            $table->string('code');
            $table->string('name');
            $table->string('slug');
            $table->string('outlet_image')->nullable();
            $table->text('address');
            $table->string('phone');
            $table->boolean('is_main')->default(false);
            $table->text('desc')->nullable();
            $table->text('footer_notes')->nullable();
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
        Schema::dropIfExists('outlets');
    }
};
