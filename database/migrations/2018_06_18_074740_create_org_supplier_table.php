<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOrgSupplierTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('org_supplier', function(Blueprint $table)
		{
			$table->integer('supplier_id')->primary();
			$table->string('supplier_code', 10)->nullable();
			$table->string('payment_code', 10)->nullable()->index('payment_code_FK');
			$table->integer('payment_mode_id')->nullable()->comment('FOB/CIF/CNF/NFC id for these');
			$table->string('supplier_name')->nullable();
			$table->string('supplier_address1')->nullable();
			$table->string('supplier_address2')->nullable();
			$table->string('supplier_city', 100)->nullable();
			$table->integer('supplier_country_id')->nullable();
			$table->string('supplier_phone', 25)->nullable();
			$table->string('supplier_fax', 25)->nullable();
			$table->string('supplier_email')->nullable();
			$table->string('contact_person')->nullable();
			$table->string('default_currency_code', 10)->nullable();
			$table->boolean('supplier_tolerance')->nullable()->default(0)->comment('This is flag , if 1 then check on tolerance table');
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
		Schema::drop('org_supplier');
	}

}
