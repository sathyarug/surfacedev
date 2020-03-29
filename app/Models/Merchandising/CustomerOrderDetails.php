<?php

namespace App\Models\Merchandising;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use App\BaseValidator;
use App\Libraries\UniqueIdGenerator;

class CustomerOrderDetails extends BaseValidator
{
    protected $table='merc_customer_order_details';
    protected $primaryKey='details_id';
    const UPDATED_AT='updated_date';
    const CREATED_AT='created_date';

    protected $fillable=['order_id','style_color'/*,'style_description'*/,'pcd','rm_in_date','po_no','planned_delivery_date',/*'revised_delivery_date',*/
  'fob','country','projection_location','order_qty','excess_presentage','planned_qty','ship_mode',
'ex_factory_date','ac_date','active_status','colour_type','cus_style_manual','fng_id'/*,'delivery_status'*/];

    protected $rules=array(
        'order_id'=>'required',
        'style_color'=>'required',
        //'style_description' => 'required',
        'pcd' => 'required',
        'rm_in_date' => 'required',
        'po_no' => 'required',
        'planned_delivery_date' => 'required',
        //'revised_delivery_date' => 'required',
        'fob' => 'required',
        'country' => 'required',
        'projection_location' => 'required',
        'order_qty' => 'required',
        'excess_presentage' => 'required',
        'planned_qty' => 'required',
        'ship_mode' => 'required',
        'fng_id'  => 'required'
    );

    public function __construct() {
        parent::__construct();
    }

    //default currency of the company
		public function color()
		{
			 return $this->belongsTo('App\Models\Org\Color' , 'style_color')->select(['color_id','color_code']);
		}

    //country of the company
		public function order_country()
		{
			 return $this->belongsTo('App\Models\Org\Country' , 'country');
		}

    //country of the company
		public function order_location()
		{
			 return $this->belongsTo('App\Models\Org\Location\Location' , 'projection_location')->select(array('loc_id', 'loc_name'));;
    }


    //get order quantity from order id
    public function getCustomerOrderQty($orderId, $colorComboId){

        // Comment On - 05/28/2019
        // Comment By - Nalin Jayakody
        // Comment For - Get SUM of sales order quantity by color combo
        // ===============================================================
        /*return DB::table('merc_customer_order_details')->select(DB::raw("order_id, SUM(order_qty) AS Order_Qty"))
              ->where('order_id','=',$orderId)
              ->where('delivery_status','RELEASED')
              ->groupBy('order_id')
              ->get();*/

        return DB::table('merc_customer_order_details')->select(DB::raw("order_id, SUM(order_qty) AS Order_Qty"))
              ->join('merc_costing_so_combine','merc_customer_order_details.details_id','merc_costing_so_combine.details_id')
              ->join('costing_bulk_feature_details','merc_costing_so_combine.feature_id','costing_bulk_feature_details.blk_feature_id')
              ->where('order_id','=',$orderId)
              ->where('delivery_status','RELEASED')
              ->where('costing_bulk_feature_details.combo_color','=',$colorComboId)
              ->groupBy('order_id')
              ->get();

    }

    //get cutomer order sizes and quantities

    public function getCustomerOrderSizes($orderId){

      return DB::table('merc_customer_order_details')
                ->join('merc_customer_order_header','merc_customer_order_header.order_id','merc_customer_order_details.order_id')
                ->join('merc_customer_order_size','merc_customer_order_size.details_id','merc_customer_order_details.details_id')
                ->join('org_size','org_size.size_id','merc_customer_order_size.size_id')
                ->select(DB::raw("org_size.size_name, SUM(merc_customer_order_size.order_qty) AS SizeQty,org_size.size_id"))
                ->where('merc_customer_order_header.order_id',$orderId)
                ->where('merc_customer_order_details.delivery_status','RELEASED')
                ->groupBy('org_size.size_id','org_size.size_name')
                ->get();

    }

    public function getCustomerColors($orderId){

        return DB::table('merc_customer_order_details')
                ->join('merc_customer_order_header','merc_customer_order_header.order_id','merc_customer_order_details.order_id')
                ->join('org_color','merc_customer_order_details.style_color','org_color.color_id')
                ->select(DB::raw("org_color.color_name,Sum(merc_customer_order_details.order_qty) AS ColorQty, org_color.color_id"))
                ->where('merc_customer_order_header.order_id',$orderId)
                ->where('merc_customer_order_details.delivery_status','RELEASED')
                ->groupBy('org_color.color_id','org_color.color_name')
                ->get();
    }

    public function getBothRatios($orderId){

        return DB::table('merc_customer_order_details')
                ->join('merc_customer_order_header','merc_customer_order_header.order_id','merc_customer_order_details.order_id')
                ->join('merc_customer_order_size','merc_customer_order_size.details_id','merc_customer_order_details.details_id')
                ->join('org_size','org_size.size_id','merc_customer_order_size.size_id')
                ->join('org_color','merc_customer_order_details.style_color','org_color.color_id')
                ->select(DB::raw("org_color.color_name,Sum(merc_customer_order_details.order_qty) AS RatioQty, org_color.color_id"))
                ->where('merc_customer_order_header.order_id',$orderId)
                ->where('merc_customer_order_details.delivery_status','RELEASED')
                ->groupBy('org_color.color_id','org_color.color_name','org_size.size_id','org_size.size_name')
                ->get();
    }


}
