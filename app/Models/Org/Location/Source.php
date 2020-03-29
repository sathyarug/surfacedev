<?php

namespace App\Models\Org\Location;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class Source extends BaseValidator
{
		protected $table = 'org_source';
		protected $primaryKey = 'source_id';
		const CREATED_AT = 'created_date';
		const UPDATED_AT = 'updated_date';

		protected $fillable = ['source_name','source_code','source_id'];

  	/*protected $rules = array(
      'source_code' => 'required',
      'source_name'  => 'required'
  	);*/

  	public function __construct()
  	{
      parent::__construct();
  	}

		//Accessors & Mutators......................................................

    public function setSourceCodeAttribute($value) {
        $this->attributes['source_code'] = strtoupper($value);
    }

    public function setSourceNameAttribute($value) {
        $this->attributes['source_name'] = strtoupper($value);
    }

		//Validation functions......................................................

    /**
    *unique:table,column,except,idColumn
    *The field under validation must not exist within the given database table
    */
    protected function getValidationRules($data /*model data with attributes*/) {
      return [
          'source_code' => [
            'required',
            'unique:org_source,source_code,'.$data['source_id'].',source_id',
          ],
          'source_name' => 'required'
      ];
    }
}
