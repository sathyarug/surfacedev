<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToOrgSupplierTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('org_supplier', function(Blueprint $table)
		{
			$table->foreign('payment_code', 'payment_code_FK')->references('payment_code')->on('fin_payment_term')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('org_supplier', function(Blueprint $table)
		{
			$table->dropForeign('payment_code_FK');
		});
	}

}
