<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOrgOriginTypeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('org_origin_type', function(Blueprint $table)
		{
			$table->integer('origin_type_id', true);
			$table->string('origin_type', 50)->index('company_code');
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
		Schema::drop('org_origin_type');
	}

}
