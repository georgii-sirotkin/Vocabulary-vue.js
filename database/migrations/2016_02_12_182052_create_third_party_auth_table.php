<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateThirdPartyAuthTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('third_party_auth', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('third_party', ['facebook', 'google']);
            $table->string('third_party_user_id');
            $table->integer('user_id')->unsigned();

            $table->unique(['third_party_user_id', 'third_party']);
            $table->unique(['user_id', 'third_party']);
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('third_party_auth');
    }
}
