<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOrgSupplierToleranceTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('org_supplier_tolerance', function(Blueprint $table)
		{
			$table->integer('tolerance_id')->primary();
			$table->integer('supplier_id')->nullable()->index('supplier_id_FK');
			$table->integer('uom_id')->nullable()->comment('UOM');
			$table->float('min_qty', 10, 0)->nullable();
			$table->float('max_qty', 10, 0)->nullable();
			$table->float('tolerance_percentage', 10, 0)->nullable();
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
		Schema::drop('org_supplier_tolerance');
	}

}
