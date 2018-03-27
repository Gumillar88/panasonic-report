<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameChannelColumnDealerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dealers', function ($table) {
            $table->renameColumn('channel_ID', 'dealer_channel_ID');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dealers', function ($table) {
            $table->renameColumn('dealer_channel_ID', 'channel_ID');
        });
    }
}
