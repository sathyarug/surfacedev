<?php

namespace App\Models\Org;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class Silhouette extends BaseValidator
{
    protected $table='product_silhouette';
    protected $primaryKey='product_silhouette_id';
    const UPDATED_AT='updated_date';
    const CREATED_AT='created_date';

    protected $fillable=['product_silhouette_description','silhouette_code','product_component'];

    // protected $rules=array(
    //     'product_silhouette_description'=>'required'
    // );

    //Validation Functions
    /**
    *unique:table,column,except,idColumn
    *The field under validation must not exist within the given database table
    **/
    protected function getValidationRules($data) {
      return [
          'silhouette_code' => [
            'required',
            'unique:product_silhouette,silhouette_code,'.$data['product_silhouette_id'].',product_silhouette_id',
          ],
          'product_silhouette_description' => 'required',
          'product_component' => 'required'
      ];
    }

    public function __construct() {
        parent::__construct();
    }
}
