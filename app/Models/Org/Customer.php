<?php

namespace App\Models\Org;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class Customer extends BaseValidator {

    protected $table = 'cust_customer';
    protected $primaryKey = 'customer_id';

    const UPDATED_AT = 'updated_date';
    const CREATED_AT = 'created_date';

    protected $fillable = ['customer_code', 'customer_name', 'customer_short_name', 'type_of_service', 'business_reg_no', 'business_reg_date', 'customer_address1', 'customer_address2', 'customer_city', 'customer_postal_code', 'customer_state', 'customer_country', 'customer_contact1', 'customer_contact2',
        'customer_contact3', 'customer_email', 'customer_map_location', 'customer_website', 'company_code', 'operation_start_date', 'order_destination', 'currency', 'boi_reg_no', 'boi_reg_date', 'vat_reg_no', 'svat_no', 'managing_director_name', 'managing_director_email', 'finance_director_name', 'finance_director_email',
        'finance_director_contact', 'additional_comments', 'ship_terms_agreed', 'payemnt_terms', 'payment_mode', 'bank_acc_no', 'bank_name', 'bank_branch', 'bank_code', 'bank_swift', 'bank_iban', 'bank_contact', 'intermediary_bank_name', 'intermediary_bank_address', 'intermediary_bank_contact', 'buyer_posting_group',
        'business_posting_group', 'approved_by', 'system_updated_by', 'customer_creation_form','customer_country', 'status'];

    protected $dates = ['operation_start_date'];
    /*protected $rules = array(
        'customer_code' => 'required',
        'customer_name' => 'required',
        'customer_short_name' => 'required',
        'type_of_service' => 'required',
        'customer_city' => 'required',
        'customer_country' => 'required',
        'currency' => 'required'
        'customer_contact1' => 'required'
    );*/

    public function __construct() {
        parent::__construct();
    }


    public function setOperationStartDateAttribute($value) {
      $this->attributes['operation_start_date'] = date('Y-m-d', strtotime($value));
    }

    /*public function getOperationStartDateAttribute($value) {
        $this->attributes['operation_start_date'] = date('d F,Y', strtotime($value));
        return $this->attributes['operation_start_date'];
    }*/
    public function setBusinessRegDateAttribute($value) {
      $this->attributes['business_reg_date'] = date('Y-m-d', strtotime($value));
    }

    /*public function getBusinessRegDateAttribute($value) {
        $this->attributes['business_reg_date'] = date('d F,Y', strtotime($value));
        return $this->attributes['business_reg_date'];
    }*/
    public function setBoiRegDateAttribute($value) {
      $this->attributes['boi_reg_date'] = date('Y-m-d', strtotime($value));
    }

    /*public function getBoiRegDateAttribute($value) {
        $this->attributes['boi_reg_date'] = date('d F,Y', strtotime($value));
        return $this->attributes['boi_reg_date'];
    }*/


    //Validation functions......................................................
    /**
    *unique:table,column,except,idColumn
    *The field under validation must not exist within the given database table
    */
    protected function getValidationRules($data /*model data with attributes*/) {
      return [
          'customer_code' => [
            'required',
            'unique:cust_customer,customer_code,'.$data['customer_id'].',customer_id',
          ],
          'customer_name' => 'required',
          'customer_short_name' => 'required',
          'type_of_service' => 'required',
          'customer_city' => 'required',
          'customer_country' => 'required',
          'currency' => 'required'
      ];
    }


    //Relationships................................................................

		//default currency of the company
		public function currency()
		{
			 return $this->belongsTo('App\Models\Finance\Currency' , 'currency');
		}

    //country of the company
		public function customerCountry()
		{
			 return $this->belongsTo('App\Models\Org\Country' , 'customer_country');
		}

    public function divisions()
    {
        return $this->belongsToMany('App\Models\Org\Division','org_customer_divisions','customer_id','division_id')
        ->withPivot('id');
    }

    static function getActiveCustomerList() {
        return Customer::select('*')
                ->where('status', '=', '1')
                ->orderBy('customer_name', 'ASC')->get();
    }




}
