<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeColumnNamesDifferentFromTableNames extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('words', function (Blueprint $table) {
            $table->renameColumn('word', 'title');
        });

        Schema::table('definitions', function (Blueprint $table) {
            $table->renameColumn('definition', 'text');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('words', function (Blueprint $table) {
            $table->renameColumn('title', 'word');
        });

        Schema::table('definitions', function (Blueprint $table) {
            $table->renameColumn('text', 'definition');
        });
    }
}
