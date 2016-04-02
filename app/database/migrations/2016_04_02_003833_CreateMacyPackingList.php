<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMacyPackingList extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('MacysPackinglists', function(Blueprint $table)
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
		Schema::drop('MacysPackinglists');
	}

}
