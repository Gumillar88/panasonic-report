<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableReportEmptystock extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_empty_stock', function (Blueprint $table) {
            $table->bigIncrements('ID');
            $table->bigInteger('promotor_ID');
            $table->bigInteger('dealer_ID');
            $table->bigInteger('product_model_ID');
            $table->integer('created');
            $table->integer('updated');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('report_empty_stock');
    }
}
