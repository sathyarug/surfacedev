<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToOrgCompanyTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('org_company', function(Blueprint $table)
		{
			$table->foreign('group_id', 'fk_group_id')->references('group_id')->on('org_group')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('org_company', function(Blueprint $table)
		{
			$table->dropForeign('fk_group_id');
		});
	}

}
