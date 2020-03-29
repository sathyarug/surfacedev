<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOrgLocationTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('org_location', function(Blueprint $table)
		{
			$table->integer('loc_id', true);
			$table->string('loc_code', 10)->index('loc_code');
			$table->integer('company_id')->index('company_code');
			$table->string('loc_name');
			$table->string('loc_type', 10)->comment('INTERNAL/EXTERNAL');
			$table->string('loc_address_1')->nullable();
			$table->string('loc_address_2')->nullable();
			$table->string('city')->nullable();
			$table->string('country_code', 5)->nullable();
			$table->string('loc_phone', 50)->nullable();
			$table->string('loc_fax', 50)->nullable();
			$table->string('time_zone')->nullable();
			$table->string('currency_code', 10)->nullable()->index('currency_code');
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
		Schema::drop('org_location');
	}

}
