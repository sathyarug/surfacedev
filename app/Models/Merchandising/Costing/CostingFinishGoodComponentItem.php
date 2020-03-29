<?php

namespace App\Models\Merchandising\Costing;

use Illuminate\Database\Eloquent\Model;
use App\Libraries\UniqueIdGenerator;
use DB;

use App\BaseValidator;

class CostingFinishGoodComponentItem extends BaseValidator {

    protected $table = 'costing_finish_good_component_items';
    protected $primaryKey = 'id';

    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';

    protected $fillable = [
      'fg_component_id', 'category_id', 'article_no', 'master_id', 'position_id', 'uom_id', 'color_id', 'supplier_id',
      'origin_type_id', 'garment_options_id', 'unit_price', 'net_consumption', 'wastage', 'gross_consumption', 'meterial_type',
      'freight_charges', 'mcq', 'surcharge', 'total_cost', 'ship_mode', 'ship_term_id', 'lead_time', 'country_id', 'comments'
  ];

    protected $rules = array(
        'fg_component_id' => 'required',
        'category_id' => 'required',
        'uom_id' => 'required',
        'unit_price' => 'required',
        'net_consumption' => 'required',
        'gross_consumption' => 'required',
        'total_cost' => 'required',
    );

}
