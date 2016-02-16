<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWordTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('word', function (Blueprint $table) {
            $table->increments('id');
            $table->string('word');
            $table->string('url_friendly_word');
            $table->integer('user_id')->unsigned()->index();
            $table->tinyInteger('right_guesses_number')->unsigned();
            $table->string('image_filename')->nullable();
            $table->timestamps();

            $table->unique(['word', 'user_id']);
            $table->unique(['url_friendly_word', 'user_id']);
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
        Schema::drop('word');
    }
}
