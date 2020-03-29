<?php

namespace App\Models\Org;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class SilhouetteClassification extends BaseValidator
{
    protected $table = 'org_silhouette_classification';
    protected $primaryKey = 'sil_class_id';
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';

    protected $fillable = ['sil_class_description'];

    // protected $rules = array(
    //     'sil_class_description' => 'required',
    // );

    //Validation Functions
    /**
    *unique:table,column,except,idColumn
    *The field under validation must not exist within the given database table
    **/
    protected function getValidationRules($data) {
      return [
          'sil_class_description' => [
            'required',
            'unique:org_silhouette_classification,sil_class_description,'.$data['sil_class_id'].',sil_class_id',
          ],
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
