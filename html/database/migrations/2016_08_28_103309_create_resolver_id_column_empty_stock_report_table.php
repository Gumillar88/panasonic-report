<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResolverIdColumnEmptyStockReportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('report_empty_stock', function ($table) {
            $table->bigInteger('resolver_ID')->after('promotor_ID');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('report_empty_stock', function ($table) {
            $table->dropColumn('resolver_ID');
        });
    }
}
