<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnTableSalesTarget extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('promotor_targets', function ($table) {
            $table->bigInteger('arco_ID')->after('dealer_ID');
        });

        Schema::table('promotor_targets', function ($table) {
            $table->bigInteger('tl_ID')->after('arco_ID');
        });

        Schema::table('promotor_targets', function ($table) {
            $table->bigInteger('account_ID')->after('tl_ID');
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('promotor_targets', function ($table) {
            $table->dropColumn(['account_ID', 'tl_ID', 'arco_ID']);
        });
    }
}
