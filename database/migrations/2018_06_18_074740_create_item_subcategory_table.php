<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateItemSubcategoryTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('item_subcategory', function(Blueprint $table)
		{
			$table->integer('subcategory_id', true);
			$table->string('subcategory_code', 10)->index('loc_code');
			$table->integer('category_id')->nullable();
			$table->string('subcategory_name', 150);
			$table->boolean('is_inspectiion_allowed')->nullable();
			$table->boolean('is_display')->nullable();
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
		Schema::drop('item_subcategory');
	}

}
