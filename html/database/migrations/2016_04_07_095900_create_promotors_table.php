<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePromotorsTable extends Migration
{/**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create table
        Schema::create('promotors', function (Blueprint $table) {
            $table->bigIncrements('ID');
            $table->bigInteger('dealer_ID');
            $table->string('phone', 255)->unique();
            $table->longText('password');
            $table->string('name', 255);
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
        Schema::drop('promotors');
    }
}
