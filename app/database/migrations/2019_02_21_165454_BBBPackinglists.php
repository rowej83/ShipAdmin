<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class BBBPackinglists extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('BBBPackinglists', function(Blueprint $table)
        {
            $table->increments('id');
            $table->timestamps();
            $table->text('pathToFile');
            $table->text('po');
            $table->text('shipterms');
        });

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
	}

}
