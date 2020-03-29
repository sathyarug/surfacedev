<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Country extends BaseValidator
{
    protected $table='org_country';
    protected $primaryKey='country_id';
    const UPDATED_AT='updated_date';
    const CREATED_AT='created_date';

    protected $fillable = ['country_code','country_description','country_id'];
    
    protected $rules = array(
        'country_code' => 'required',
        'country_description'  => 'required'        
    );
    
    public function __construct()
    {
        parent::__construct();
        $this->attributes = array(
            'updated_by' => 2//Session::get("user_id")
        );
    }
}
