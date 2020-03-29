<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToOrgStoreBinAllocationTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('org_store_bin_allocation', function(Blueprint $table)
		{
			$table->foreign('store_bin_id', 'store_bin_id')->references('store_bin_id')->on('org_store_bin')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('org_store_bin_allocation', function(Blueprint $table)
		{
			$table->dropForeign('store_bin_id');
		});
	}

}
