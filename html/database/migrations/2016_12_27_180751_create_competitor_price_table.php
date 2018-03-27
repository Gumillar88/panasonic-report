<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompetitorPriceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('competitor_prices', function (Blueprint $table) {
            $table->bigIncrements('ID');
            $table->bigInteger('promotor_ID');
            $table->bigInteger('dealer_ID');
            $table->bigInteger('competitor_brand_ID');
            $table->string('competitor_brand_custom');
            $table->bigInteger('product_category_ID');
            $table->string('model_name');
            $table->bigInteger('price_normal');
            $table->bigInteger('price_promo');
            $table->date('date');
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
        Schema::drop('competitor_prices');
    }
}
