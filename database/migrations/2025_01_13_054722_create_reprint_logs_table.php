<?php

use App\Models\Sales;
use App\Models\User;
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
        Schema::create('reprint_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Sales::class);
            $table->foreignIdFor(User::class);
            $table->dateTime('reprint_time');
            $table->string('reason')->nullable();
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
        Schema::dropIfExists('reprint_logs');
    }
};
