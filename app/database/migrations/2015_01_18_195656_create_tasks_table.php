<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTasksTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('tasks', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('job_id');
			$table->string('order_id');
			$table->string('status');
			$table->string('process');
			$table->string('vendor');
			$table->string('days');
			$table->string('deadline');
			$table->string('completion')->nullable();
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
		Schema::table('tasks', function(Blueprint $table)
		{
			Schema::drop('tasks');
		});
	}

}
