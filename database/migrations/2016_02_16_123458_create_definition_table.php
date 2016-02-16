<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDefinitionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('definition', function (Blueprint $table) {
            $table->increments('id');
            $table->text('definition');
            $table->integer('word_id')->unsigned()->index();

            $table->foreign('word_id')->references('id')->on('word')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('definition');
    }
}
