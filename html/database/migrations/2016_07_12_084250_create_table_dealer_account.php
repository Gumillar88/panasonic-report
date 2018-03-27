<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableDealerAccount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dealer_accounts', function (Blueprint $table) {
            $table->bigIncrements('ID');
            $table->string('name');
            $table->bigInteger('parent_ID');
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
        Schema::drop('dealer_accounts');
    }
}
