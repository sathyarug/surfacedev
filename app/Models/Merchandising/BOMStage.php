<?php

namespace App\Models\Merchandising;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class BOMStage extends BaseValidator
{
    protected $table='merc_bom_stage';
    protected $primaryKey='bom_stage_id';
    const UPDATED_AT='updated_date';
    const CREATED_AT='created_date';

    protected $fillable=['bom_stage_description','bom_stage_id'];

    // protected $rules=array(
    //     'bom_stage_description'=>'required'
    // );

    //Validation Functions
    /**
    *unique:table,column,except,idColumn
    *The field under validation must not exist within the given database table
    **/
    protected function getValidationRules($data) {
      return [
          'bom_stage_description' => [
            'required',
            'unique:merc_bom_stage,bom_stage_description,'.$data['bom_stage_id'].',bom_stage_id',
          ],
      ];
    }

    public function __construct() {
        parent::__construct();
    }
}
