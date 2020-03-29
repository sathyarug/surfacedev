<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOrgCostCenterTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('org_cost_center', function(Blueprint $table)
		{
			$table->integer('cost_center_id')->primary();
			$table->string('cost_center_code', 10)->nullable();
			$table->integer('loc_id')->nullable()->index('fk_location_id');
			$table->string('cost_center_name', 100)->nullable();
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
		Schema::drop('org_cost_center');
	}

}
