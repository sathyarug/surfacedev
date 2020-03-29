<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOrgSmvLadderTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('org_smv_ladder', function(Blueprint $table)
		{
			$table->integer('smv_ladder_id', true);
			$table->integer('qty_ladder_id')->nullable();
			$table->float('smv_from', 10, 0)->nullable();
			$table->float('smv_to', 10, 0);
			$table->float('efficiency', 10, 0)->nullable();
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
		Schema::drop('org_smv_ladder');
	}

}
