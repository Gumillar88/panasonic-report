<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTargetTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create table
        Schema::create('promotor_targets', function (Blueprint $table) {
            $table->bigIncrements('ID');
            $table->bigInteger('promotor_ID');
            $table->bigInteger('dealer_ID');
            $table->bigInteger('product_ID');
            $table->bigInteger('total');
            $table->string('month');
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
        Schema::drop('promotor_targets');
    }
}
