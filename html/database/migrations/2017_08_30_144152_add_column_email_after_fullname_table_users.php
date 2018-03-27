<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnEmailAfterFullnameTableUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function ($table) {
            $table->string('email')->after('fullname');
        });

        DB::table('users')->insert([
            'fullname' => 'admin',
            'email'    => 'admin@havas.com',
            'password' => '$2y$10$1MRGzcmmOo8.LVtd0Rf5G.9LU/ji3K.Eq2PTaeSbJO7U0pH.DHV6O',
            'type'     => 'admin',
            'created'  => '1474010815',
            'updated'  => '1474010815'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function ($table) {
            $table->dropColumn('email');
        });
    }
}
