<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeDashboardEmailAndTokenUnique extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dashboard_accounts', function ($table) {
            $table->unique('email');
        });
        
        Schema::table('dashboard_token', function ($table) {
            $table->unique('token');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dashboard_accounts', function ($table) {
            $table->dropUnique('email');
        });
        
        Schema::table('dashboard_token', function ($table) {
            $table->dropUnique('token');
        });
    }
}
