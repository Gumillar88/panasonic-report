<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditStructureDealerAccount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dealer_accounts', function ($table) {
            $table->renameColumn('parent_ID', 'branch_ID');
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
            $table->renameColumn('branch_ID', 'parent_ID');
        });
    }
}
