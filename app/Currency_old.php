<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Currency extends BaseValidator
{
    protected $table = 'fin_currency';
    protected $primaryKey = 'currency_id';
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';
    
    protected $fillable = ['currency_code','currency_description','currency_id'];
    
    protected $rules = array(
        'currency_code' => 'required',
        'currency_description'  => 'required'        
    );
    
    public function __construct()
    {
        parent::__construct();
        $this->attributes = array(
            'updated_by' => 2//Session::get("user_id")
        );
    }
    
    
}
