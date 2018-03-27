<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModelTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create table
        Schema::create('product_models', function (Blueprint $table) {
            $table->bigIncrements('ID');
            $table->bigInteger('product_type_ID');
            $table->string('name', 255);
            $table->string('code', 255)->unique();
            $table->longText('subcategory');
            $table->double('price');
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
        Schema::drop('product_models');
    }
}
