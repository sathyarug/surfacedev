<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCustDivisionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cust_division', function(Blueprint $table)
		{
			$table->integer('division_id')->primary();
			$table->string('division_code', 10)->nullable();
			$table->string('customer_code', 10)->nullable();
			$table->string('division_description', 100)->nullable();
			$table->dateTime('created_date')->nullable();
			$table->integer('created_by')->nullable();
			$table->dateTime('updated_date')->nullable();
			$table->integer('updated_by')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('cust_division');
	}

}
