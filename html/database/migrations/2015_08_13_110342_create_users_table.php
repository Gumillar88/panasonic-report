<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\Http\Models\UserModel as User;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create table
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('ID');
            $table->string('fullname', 255);
            $table->string('username', 255)->unique();
            $table->longText('password');
            $table->string('type', 255);
            $table->integer('created');
            $table->integer('updated');
        });
        
        
        // Fill dummy admin user data
        $name = 'admin';
        $user = new User();
        $user->create($name, $name, Hash::make($name), $name);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('users');
    }
}
