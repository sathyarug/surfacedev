<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToOrgGroupTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('org_group', function(Blueprint $table)
		{
			$table->foreign('source_id', 'fk_org_group_org_source')->references('source_id')->on('org_source')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('org_group', function(Blueprint $table)
		{
			$table->dropForeign('fk_org_group_org_source');
		});
	}

}
