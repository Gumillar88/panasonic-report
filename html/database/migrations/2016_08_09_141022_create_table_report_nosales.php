<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableReportNosales extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_nosale', function (Blueprint $table) {
            $table->bigIncrements('ID');
            $table->bigInteger('promotor_ID');
            $table->bigInteger('dealer_ID');
            $table->bigInteger('account_ID');
            $table->bigInteger('tl_ID');
            $table->bigInteger('arco_ID');
            $table->date('date');
            $table->integer('created');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('report_nosale');
    }
}
