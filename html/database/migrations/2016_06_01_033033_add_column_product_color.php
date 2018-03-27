<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnProductColor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_models', function ($table) {
            $table->string('color')->after('price');
        });

        Schema::table('reports', function ($table) {
            $table->string('custom_name')->after('product_model_ID');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_models', function ($table) {
            $table->dropColumn('color');
        });

        Schema::table('reports', function ($table) {
            $table->dropColumn('custom_name');
        });
    }
}
