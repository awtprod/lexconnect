<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCourtsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('courts', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('state');
			$table->string('zip');
			$table->string('court');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('courts', function(Blueprint $table)
		{
			Schema::drop('courts');
		});
	}

}
