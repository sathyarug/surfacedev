<?php

namespace App\Models\Org;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class Section extends BaseValidator
{
    protected $table = 'org_section';
    protected $primaryKey = 'section_id';

    const UPDATED_AT = 'updated_date';
    const CREATED_AT = 'created_date';

    protected $fillable = ['section_code', 'section_name', 'section_id'];
    /*protected $rules = array(
        'section_code' => 'required',
        'section_name' => 'required'
    );*/

   public function __construct() {
        parent::__construct();
    }

    //Accessors & Mutators......................................................

    public function setSectionCodeAttribute($value) {
        $this->attributes['section_code'] = strtoupper($value);
    }

    public function setSectionNameAttribute($value) {
        $this->attributes['section_name'] = strtoupper($value);
    }

    //Validation functions......................................................

    /**
    *unique:table,column,except,idColumn
    *The field under validation must not exist within the given database table
    */
    protected function getValidationRules($data /*model data with attributes*/) {
      return [
          'section_code' => [
            'required',
            'unique:org_section,section_code,'.$data['section_id'].',section_id',
          ],
          'section_name' => 'required'
      ];
    }
}
