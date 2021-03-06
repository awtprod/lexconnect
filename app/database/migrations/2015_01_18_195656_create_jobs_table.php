<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJobsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('jobs', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('order_id');
			$table->string('defendant');
			$table->string('servee_id');
			$table->string('street');
			$table->string('street2');
			$table->string('city');
			$table->string('state');
			$table->string('zipcode');
			$table->string('client');
			$table->string('vendor');
			$table->string('status');
			$table->string('completed')->nullable();
			$table->string('proof');
			$table->string('declaration');
			$table->string('proof_text');
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
		Schema::table('jobs', function(Blueprint $table)
		{
			Schema::drop('jobs');
		});
	}

}
