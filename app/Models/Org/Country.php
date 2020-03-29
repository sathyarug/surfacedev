<?php

namespace App\Models\Org;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class Country extends BaseValidator
{
    protected $table='org_country';
    protected $primaryKey='country_id';
    const UPDATED_AT='updated_date';
    const CREATED_AT='created_date';

    protected $fillable = ['country_code','country_description','country_id'];

    /*protected $rules = array(
        'country_code' => 'required',
        'country_description'  => 'required'
    );*/

    public function __construct() {
        parent::__construct();
    }

    //Accessors & Mutators......................................................

    public function setCountryCodeAttribute($value) {
        $this->attributes['country_code'] = strtoupper($value);
    }

    public function setCountryDescriptionAttribute($value) {
        $this->attributes['country_description'] = strtoupper($value);
    }

    //Validation functions......................................................

    /**
    *unique:table,column,except,idColumn
    *The field under validation must not exist within the given database table
    */
    protected function getValidationRules($data /*model data with attributes*/) {
      return [
          'country_code' => [
            'required',
            'unique:org_country,country_code,'.$data['country_id'].',country_id',
          ],
          'country_description' => 'required'
      ];
    }
}
