<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoicesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('invoices', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('order_id');
			$table->string('job_id');
			$table->string('servee_id');
			$table->string('client_amt');
			$table->string('vendor_amt');
			$table->string('vendor');
			$table->string('client');
			$table->string('invoice');
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
		Schema::table('invoices', function(Blueprint $table)
		{
			Schema::drop('invoices');
		});
	}

}
