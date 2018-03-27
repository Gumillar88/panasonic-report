<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveProductTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::drop('product_types');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('product_types', function (Blueprint $table) {
            $table->bigIncrements('ID');
            $table->bigInteger('category_ID');
            $table->string('name', 255);
            $table->integer('created');
            $table->integer('updated');
        });
    }
}
