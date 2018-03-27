<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePromotorMetaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promotor_meta', function (Blueprint $table) {
            $table->bigIncrements('ID');
            $table->bigInteger('promotor_ID');
            $table->string('name');
            $table->longText('content');
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
        Schema::drop('promotor_meta');
    }
}
