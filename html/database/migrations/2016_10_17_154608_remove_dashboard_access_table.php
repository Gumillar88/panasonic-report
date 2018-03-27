<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveDashboardAccessTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::drop('dashboard_access');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('dashboard_access', function (Blueprint $table) {
            $table->bigIncrements('ID');
            $table->string('email');
            $table->string('code', 255);
            $table->integer('created');
        });
    }
}
