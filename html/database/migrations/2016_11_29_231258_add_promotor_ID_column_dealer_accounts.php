<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPromotorIDColumnDealerAccounts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dealer_accounts', function ($table) {
            $table->bigInteger('promotor_ID')->after('branch_ID');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dealer_accounts', function ($table) {
            $table->dropColumn('promotor_ID');
        });
    }
}
