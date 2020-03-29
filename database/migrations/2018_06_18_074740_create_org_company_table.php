<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOrgCompanyTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('org_company', function(Blueprint $table)
		{
			$table->integer('company_id', true);
			$table->string('company_code', 10)->index('company_code');
			$table->integer('group_id')->nullable()->index('group_code');
			$table->string('company_name')->nullable();
			$table->string('company_address_1')->nullable();
			$table->string('company_address_2')->nullable();
			$table->string('city')->nullable();
			$table->string('country_code', 5)->nullable();
			$table->string('company_fax', 50)->nullable();
			$table->string('company_contact_1', 50)->nullable();
			$table->string('company_contact_2', 50)->nullable();
			$table->string('company_logo', 100)->nullable();
			$table->string('company_email', 100)->nullable();
			$table->string('company_web', 100)->nullable();
			$table->string('default_currency', 50)->nullable();
			$table->string('finance_month')->nullable()->comment('JAN to DES/MAR to APR');
			$table->boolean('status')->nullable();
			$table->text('company_remarks', 65535)->nullable();
			$table->string('vat_reg_no', 50)->nullable();
			$table->string('tax_code', 50)->nullable();
			$table->string('company_reg_no', 50)->nullable();
			$table->dateTime('create_date')->nullable();
			$table->integer('created_by')->nullable();
			$table->dateTime('update_date')->nullable();
			$table->integer('updated_date')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('org_company');
	}

}
