<?php

namespace App\Models\Org\Cancellation;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use App\BaseValidator;

class CancellationCategory extends BaseValidator
{
    protected $table = 'org_cancellation_category';
    protected $primaryKey = 'category_id';

    const UPDATED_AT = 'updated_date';
    const CREATED_AT = 'created_date';

    protected $fillable = ['category_code', 'category_description', 'category_id'];

    /*protected $rules = array(
        'category_code' => [
          'required',
          'unique:org_cancellation_category,category_code,'.$this->attributes['category_id'] ,
        ],
        'category_description' => 'required'
    );*/

    public function __construct() {
         parent::__construct();
    }

    //Accessors & Mutators......................................................

    public function setCategoryDescriptionAttribute($value) {
        $this->attributes['category_description'] = strtoupper($value);
    }

    public function setCategoryCodeAttribute($value) {
        $this->attributes['category_code'] = strtoupper($value);
    }

    //Validation functions......................................................
    /**
    *unique:table,column,except,idColumn
    *The field under validation must not exist within the given database table
    */
    protected function getValidationRules($data /*model data with attributes*/) {
      return [
          'category_code' => [
            'required',
            'unique:org_cancellation_category,category_code,'.$data['category_id'].',category_id',
          ],
          'category_description' => 'required'
      ];
    }


}
