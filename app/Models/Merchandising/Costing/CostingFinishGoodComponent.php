<?php

namespace App\Models\Merchandising\Costing;

use Illuminate\Database\Eloquent\Model;
use App\Libraries\UniqueIdGenerator;
use DB;

use App\BaseValidator;

class CostingFinishGoodComponent extends BaseValidator {

    protected $table = 'costing_finish_good_components';
    protected $primaryKey = 'id';

    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';

    protected $fillable = ['product_component_id', 'product_silhouette_id', 'line_no', 'surcharge', 'color', 'mcq', 'smv', 'status'];

    protected $rules = array(
      /*  'style_id' => 'required',
        'bom_stage_id' => 'required',
        'season_id' => 'required',      */
    );

}
