<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('orders', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('user');
			$table->string('defendant');
			$table->string('company');
			$table->string('plaintiff');
			$table->string('reference');
			$table->string('case');
			$table->string('state');
			$table->string('court');
			$table->string('completed');
			$table->string('filed_docs');
			$table->string('status');
			$table->string('rec_docs');
			$table->string('instrument');
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
		Schema::table('orders', function(Blueprint $table)
		{
			Schema::drop('orders');
		});
	}

}
