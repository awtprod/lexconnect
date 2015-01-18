<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('serve', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('order_id');
			$table->string('job_id');
			$table->string('servee_id');
			$table->string('date');
			$table->string('time');
			$table->string('served_upon');
			$table->string('age');
			$table->string('gender');
			$table->string('race');
			$table->string('height');
			$table->string('weight');
			$table->string('hair');
			$table->string('moustache');
			$table->string('glasses');
			$table->string('beard');
			$table->string('relationship');
			$table->string('sub_served');
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
		Schema::table('serve', function(Blueprint $table)
		{
			Schema::drop('serve');
		});
	}

}
