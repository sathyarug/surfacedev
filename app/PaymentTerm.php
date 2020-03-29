<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentTerm extends BaseValidator
{
    protected $table = 'fin_payment_term';
    protected $primaryKey = 'payment_term_id';
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';
    
    protected $fillable = ['payment_code','payment_description','payment_term_id'];
    
    protected $rules = array(
        'payment_code' => 'required',
        'payment_description'  => 'required'        
    );
    
    public function __construct()
    {
        parent::__construct();
        $this->attributes = array(
            'updated_by' => 2//Session::get("user_id")
        );
    }
    
    
}
