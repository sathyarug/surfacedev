<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToOrgCostCenterTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('org_cost_center', function(Blueprint $table)
		{
			$table->foreign('loc_id', 'fk_location_id')->references('loc_id')->on('org_location')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('org_cost_center', function(Blueprint $table)
		{
			$table->dropForeign('fk_location_id');
		});
	}

}
