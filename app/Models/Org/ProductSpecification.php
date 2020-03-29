<?php

namespace App\Models\Org;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class ProductSpecification extends BaseValidator
{
    protected $table = 'prod_category';
    protected $primaryKey = 'prod_cat_id';
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';

    protected $fillable = ['prod_cat_description','category_code'];

    // protected $rules = array(
    //     'prod_cat_description' => 'required',
    // );

    //Validation Functions
    /**
    *unique:table,column,except,idColumn
    *The field under validation must not exist within the given database table
    **/
    protected function getValidationRules($data) {
      return [
          'category_code' => [
            'required',
            'unique:prod_category,category_code,'.$data['prod_cat_id'].',prod_cat_id',
          ],
          'prod_cat_description' => 'required',
      ];
    }

    public function __construct()
    {
        parent::__construct();
        $this->attributes = array(
            'updated_by' => 2//Session::get("user_id")
          );
    }


}
