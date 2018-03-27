<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewsDealerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dealer_news', function (Blueprint $table) {
            $table->bigIncrements('ID');
            $table->bigInteger('news_ID');
            $table->bigInteger('dealer_ID');
            $table->integer('created');
        });
        
        Schema::table('news', function ($table) {
            $table->dropColumn('dealer_ID');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('dealer_news');
        
        Schema::table('news', function ($table) {
            $table->text('dealer_ID')->after('ID');
        });
    }
}
