<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToOrgStoreBinTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('org_store_bin', function(Blueprint $table)
		{
			$table->foreign('store_id', 'fk_store_id')->references('store_id')->on('org_store')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('org_store_bin', function(Blueprint $table)
		{
			$table->dropForeign('fk_store_id');
		});
	}

}
