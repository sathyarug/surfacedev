<?php

namespace App\Models\Org;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class Color extends BaseValidator
{
    protected $table = 'org_color';
    protected $primaryKey = 'color_id';

    const UPDATED_AT = 'updated_date';
    const CREATED_AT = 'created_date';

    protected $fillable = ['color_code','color_name','color_id','color_category','col_quality'];

   /*protected $rules=array(
        'color_code'=>'required',
        'color_name'=>'required'
    );*/

    public function __construct() {
        parent::__construct();
    }

    //Validation functions......................................................
    /**
    *unique:table,column,except,idColumn
    *The field under validation must not exist within the given database table
    */
    protected function getValidationRules($data /*model data with attributes*/) {
      return [
          'color_code' => [
            'required',
            'unique:org_color,color_code,'.$data['color_id'].',color_id',
          ],
          'color_name' => 'required'
      ];
    }

}
