<?php

namespace App\Models\Merchandising\Costing;

use Illuminate\Database\Eloquent\Model;
use App\Libraries\UniqueIdGenerator;
use DB;

use App\BaseValidator;

class CostingItem extends BaseValidator {

    protected $table = 'costing_items';
    protected $primaryKey = 'costing_item_id';

    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';

    protected $fillable = [
      'feature_component_id', 'inventory_part_id', 'position_id', 'purchase_uom_id', 'origin_type_id', 'garment_options_id',
      'unit_price', 'purchase_price', 'net_consumption', 'wastage', 'gross_consumption', /*'meterial_type',*/ 'freight_charges', 'mcq',
      'surcharge', 'total_cost', 'ship_mode', 'ship_term_id', 'lead_time', 'country_id', 'comments','costing_id',
      'product_component_id', 'product_silhouette_id','product_component_line_no', 'supplier_id'
  ];

    protected $rules = array(
        //'feature_component_id' => 'required',
        'purchase_uom_id' => 'required',
        'unit_price' => 'required',
        'net_consumption' => 'required',
        'gross_consumption' => 'required',
        'total_cost' => 'required',
    );

}
