<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReprojectionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('reprojections', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('order_id');
			$table->string('job_id');
			$table->string('task_id');
			$table->string('servee_id');
			$table->string('reprojected');
			$table->longtext('description');
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
		Schema::table('reprojections', function(Blueprint $table)
		{
			Schema::drop('reprojections');
		});
	}

}
