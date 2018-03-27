<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnDealer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dealers', function ($table) {
            $table->string('company',255)->after('name');
        });

        Schema::table('dealers', function ($table) {
            $table->string('address',255)->after('company');
        });

        Schema::table('dealers', function ($table) {
            $table->integer('dealer_type_ID')->after('ID');
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
            $table->dropColumn('company');
        });

        Schema::table('dealers', function ($table) {
            $table->dropColumn('address');
        });

        Schema::table('dealers', function ($table) {
            $table->dropColumn('dealer_type_ID');
        });
    }
}
