<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductPriceLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_price_logs', function (Blueprint $table) {
            $table->bigIncrements('ID');
            $table->bigInteger('product_ID');
            $table->bigInteger('product_type_ID');
            $table->bigInteger('product_channel_ID');
            $table->bigInteger('old');
            $table->bigInteger('new');
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
        Schema::drop('product_price_logs');
    }
}
