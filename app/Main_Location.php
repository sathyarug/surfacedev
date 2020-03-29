<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Main_Location extends BaseValidator
{
	
		protected $table = 'org_company';
		protected $primaryKey = 'company_id';
		const CREATED_AT = 'created_date';
		const UPDATED_AT = 'updated_date';
   
		protected $fillable = ['company_id','company_code','group_id','company_name','company_address_1','company_address_2','city','country_code','company_fax','company_contact_1','company_contact_2','company_logo','company_email','company_web','default_currency','finance_month','company_remarks','vat_reg_no','tax_code','company_reg_no'];
    
    	protected $rules = array(
        'company_id' => 'required',
        'company_code' => 'required',
        'company_name' => 'required',
        'group_id' => 'required',
        'company_logo' => 'required',
        'company_address_1' => 'required',
        'company_address_2' => 'required',
        'city' => 'required',
        'country_code' => 'required',
        'company_reg_no' => 'required',
        'company_contact_1' => 'required',
        'company_email' => 'required',
        'default_currency' => 'required',
        'finance_month' => 'required',
        'vat_reg_no'  => 'required',
        'tax_code'  => 'required'       
    	);
    
    	public function __construct()
    	{
        parent::__construct();
        $this->attributes = array(
            'updated_by' => 2//Session::get("user_id")
        );
    	}
}
