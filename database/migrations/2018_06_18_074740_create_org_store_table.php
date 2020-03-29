<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOrgStoreTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('org_store', function(Blueprint $table)
		{
			$table->integer('store_id', true);
			$table->integer('loc_id')->index('loc_code');
			$table->string('store_name', 100)->index('company_code');
			$table->text('store_address', 65535)->nullable();
			$table->string('store_phone', 50)->nullable();
			$table->string('store_fax', 50)->nullable();
			$table->boolean('status')->nullable();
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
		Schema::drop('org_store');
	}

}
