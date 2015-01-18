<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRulesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('rules', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name');
			$table->string('abbrev');
			$table->string('affidavit');
			$table->string('mailing');
			$table->string('filing_client');
			$table->string('filing_vendor');
			$table->string('service_client');
			$table->string('service_vendor');
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('rules', function(Blueprint $table)
		{
			Schema::drop('rules');
		});
	}

}
