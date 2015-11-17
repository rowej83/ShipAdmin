<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGroundToPackinglistsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('packinglists', function(Blueprint $table)
		{
			//
			$table->text('shipterms')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('packinglists', function(Blueprint $table)
		{
			//
			$table->dropColumn('shipterms');
		});
	}

}
