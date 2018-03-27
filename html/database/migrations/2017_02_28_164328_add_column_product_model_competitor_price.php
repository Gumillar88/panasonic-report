<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnProductModelCompetitorPrice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('competitor_prices', function (Blueprint $table) {
            $table->bigInteger('product_model_ID')->after('dealer_ID');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('competitor_prices', function (Blueprint $table) {
            $table->dropColumn('product_model_ID');
        });
    }
}
