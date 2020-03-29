<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrgSupplier extends BaseValidator
{
    protected $table='org_supplier';
    protected $primaryKey='supplier_id';
    const UPDATED_AT='updated_date';
    const CREATED_AT='created_date';

//    protected $fillable = ['supplier_code','payment_code','payment_mode_id','supplier_name','supplier_address1','supplier_address2','supplier_city'
//    ,'supplier_country_id','supplier_phone','supplier_fax','supplier_email','contact_person','default_currency_code','supplier_tolerance'];

    protected $fillable = ['supplier_code','payment_code','supplier_name','supplier_address1','supplier_address2','supplier_city'
        ,'supplier_country_id','supplier_phone','supplier_fax','supplier_email'];


    protected $rules = array(
        'supplier_code' => 'required',
        'payment_code'  => 'required',
//        'payment_mode_id'  => 'required',
        'supplier_name'  => 'required',
        'supplier_address1'  => 'required',
        'supplier_city'  => 'required',
        'supplier_country_id'  => 'required',
       'supplier_email'  => 'required|email',
        'supplier_phone'  => 'required',
//        'default_currency_code'  => 'required',
    );

    public function __construct()
    {
        parent::__construct();
        $this->attributes = array(
            'updated_by' => 2//Session::get("user_id")
        );
    }
}
