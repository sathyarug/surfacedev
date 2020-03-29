<?php

namespace App\Models\Merchandising;

use Illuminate\Database\Eloquent\Model;

use App\BaseValidator;
use App\Libraries\UniqueIdGenerator;

class PurchaseOrderGeneralDetails extends BaseValidator
{
    protected $table='merc_po_order_general_details';
    protected $primaryKey='id';
    const UPDATED_AT='updated_date';
    const CREATED_AT='created_date';

    protected $fillable=['po_no','item_code','unit_price','uom','req_qty','deli_date','remarks','status','tot_qty'];

    protected $rules=array(
      /*  'order_id'=>'required',
        'style_color'=>'required',
        'style_description' => 'required',
        'pcd' => 'required',
        'rm_in_date' => 'required',
        'po_no' => 'required',
        'planned_delivery_date' => 'required',
        'revised_delivery_date' => 'required',
        'fob' => 'required',
        'country' => 'required',
        'projection_location' => 'required',
        'order_qty' => 'required',
        'excess_presentage' => 'required'.
        'planned_qty' => 'required',
        'ship_mode' => 'required',
        'delivery_status' => 'required'*/
    );

    public function __construct() {
        parent::__construct();
    }

}
