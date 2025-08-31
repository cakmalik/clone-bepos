<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('profil_companies', function (Blueprint $table) {
            $table->timestamp('start_time')->nullable()->after('status'); 
            $table->integer('trial_duration')->nullable()->after('start_time'); 
        });
    }
    
    public function down()
    {
        Schema::table('profil_companies', function (Blueprint $table) {
            $table->dropColumn(['start_time', 'trial_duration']);
        });
    }
    
};
