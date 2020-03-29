<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOrgStoreBinAllocationTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('org_store_bin_allocation', function(Blueprint $table)
		{
			$table->integer('allocation_id', true);
			$table->integer('store_bin_id')->nullable()->index('store_bin_id');
			$table->integer('subcategory_id')->nullable();
			$table->integer('uom_id')->nullable();
			$table->float('max_capacity', 10, 0)->nullable();
			$table->float('available_capacity', 10, 0)->nullable();
			$table->boolean('status')->nullable()->default(0);
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
		Schema::drop('org_store_bin_allocation');
	}

}
