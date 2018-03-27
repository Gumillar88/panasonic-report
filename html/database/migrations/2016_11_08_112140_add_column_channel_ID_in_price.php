<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnChannelIDInPrice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_price', function ($table) {
            $table->bigInteger('dealer_channel_ID')->after('dealer_type_ID');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_price', function ($table) {
            $table->dropColumn('dealer_channel_ID');
        });
    }
}
