<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToOrgSupplierToleranceTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('org_supplier_tolerance', function(Blueprint $table)
		{
			$table->foreign('supplier_id', 'supplier_id_FK')->references('supplier_id')->on('org_supplier')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('org_supplier_tolerance', function(Blueprint $table)
		{
			$table->dropForeign('supplier_id_FK');
		});
	}

}
