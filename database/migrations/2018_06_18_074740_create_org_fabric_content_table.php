<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOrgFabricContentTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('org_fabric_content', function(Blueprint $table)
		{
			$table->integer('content_id', true);
			$table->string('content_code', 10);
			$table->string('content_name');
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
		Schema::drop('org_fabric_content');
	}

}
