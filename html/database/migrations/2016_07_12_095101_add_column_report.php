<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnReport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reports', function ($table) {
            $table->bigInteger('account_ID')->after('promotor_ID');
        });

        Schema::table('reports', function ($table) {
            $table->bigInteger('tl_ID')->after('account_ID');
        });

        Schema::table('reports', function ($table) {
            $table->bigInteger('arco_ID')->after('tl_ID');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reports', function ($table) {
            $table->dropColumn(['account_ID', 'tl_ID', 'arco_ID']);
        });
    }
}
