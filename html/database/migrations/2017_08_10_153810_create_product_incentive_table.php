<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductIncentiveTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_incentives', function (Blueprint $table) {
            $table->bigIncrements('ID');
            $table->bigInteger('product_model_ID');
            $table->bigInteger('dealer_channel_ID');
            $table->bigInteger('value');
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
        Schema::drop('product_incentives');
    }
}
