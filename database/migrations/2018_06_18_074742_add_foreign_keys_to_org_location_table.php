<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToOrgLocationTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('org_location', function(Blueprint $table)
		{
			$table->foreign('company_id', 'fk_company_id')->references('company_id')->on('org_company')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('org_location', function(Blueprint $table)
		{
			$table->dropForeign('fk_company_id');
		});
	}

}
