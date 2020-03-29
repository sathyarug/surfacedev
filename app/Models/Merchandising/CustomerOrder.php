<?php

namespace App\Models\Merchandising;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use App\BaseValidator;
use App\Libraries\UniqueIdGenerator;

class CustomerOrder extends BaseValidator
{
    protected $table='merc_customer_order_header';
    protected $primaryKey='order_id';
    const UPDATED_AT='updated_date';
    const CREATED_AT='created_date';

    protected $fillable=['order_style','order_customer','order_division','order_status','order_buy_name','order_season','order_stage','buy_id','lot_number'];

    protected $rules=array(
        'order_style'=>'required',
        'order_customer'=>'required',
        'order_division' => 'required',
        'order_stage' => 'required',
        'order_season' => 'required',

    );

    public function __construct() {
        parent::__construct();
    }


    public static function boot()
    {
        static::creating(function ($model) {
          $location = auth()->payload()['loc_id'];
          $code = UniqueIdGenerator::generateUniqueId('CUSTOMER_ORDER' , $location);
          $model->order_code = $code;
          //$model->updated_by = $user->user_id;
        });

        /*static::updating(function ($model) {
            $user = auth()->user();
            $model->updated_by = $user->user_id;
        });*/

        parent::boot();
    }


    //Validation functions......................................................

    /**
    *unique:table,column,except,idColumn
    *The field under validation must not exist within the given database table
    */
    protected function getValidationRules($data /*model data with attributes*/) {
      return [
        'order_style'=>'required',
        'order_customer'=>'required',
        'order_division' => 'required',
        'order_stage' => 'required',
        'order_season' => 'required'
      ];
    }

    //retationships.............................................................

		public function style()
		{
			 return $this->belongsTo('App\Models\Merchandising\StyleCreation' , 'order_style')->select(['style_id','style_no','style_description']);
		}

    public function customer()
		{
			 return $this->belongsTo('App\Models\Org\Customer' , 'order_customer')
          ->select(['customer_id','customer_code','customer_name']);//->with(['divisions']);
    }

    public function division()
		{
			 return $this->belongsTo('App\Models\Org\Division' , 'order_division')->select(['division_id','division_description','division_code']);
    }

    public function buyname()
		{
			 return $this->belongsTo('App\Models\Merchandising\BuyMaster' , 'buy_id')
       ->select(['buy_id','buy_name']);
    }


    public function getCustomerOrders($costingId, $colorComboId){


        // Comment On - 05/28/2019
        // Comment By - Nalin Jayakody
        // Comment For - Load sales order combine details, by combo color
        // ===============================================================
        /*return DB::table('merc_customer_order_header')
                 ->join('merc_customer_order_details','merc_customer_order_details.order_id','merc_customer_order_header.order_id')
                 ->join('merc_costing_so_combine','merc_costing_so_combine.details_id','merc_customer_order_details.details_id')
                 ->join('costing_bulk','costing_bulk.bulk_costing_id','merc_costing_so_combine.costing_id')
                 ->select('merc_customer_order_header.order_id', 'merc_customer_order_header.order_code')
                 ->where('costing_bulk.bulk_costing_id','=',$costingId)
                 ->where('merc_customer_order_details.delivery_status','=','RELEASED')
                 ->whereNotIn('merc_customer_order_header.order_id', DB::table('bom_so_allocation')->pluck('bom_so_allocation.order_id'))
                 ->groupBy('merc_customer_order_header.order_id','merc_customer_order_header.order_code')->get();*/


        return DB::table('merc_customer_order_header')
                 ->join('merc_customer_order_details','merc_customer_order_details.order_id','merc_customer_order_header.order_id')
                 ->join('merc_costing_so_combine','merc_costing_so_combine.details_id','merc_customer_order_details.details_id')
                 ->join('costing_bulk','costing_bulk.bulk_costing_id','merc_costing_so_combine.costing_id')
                 ->join('costing_bulk_feature_details','merc_costing_so_combine.feature_id','costing_bulk_feature_details.blk_feature_id')
                 ->select('merc_customer_order_header.order_id', 'merc_customer_order_header.order_code','costing_bulk_feature_details.combo_color')
                 ->where('costing_bulk.bulk_costing_id','=',$costingId)
                 ->where('merc_customer_order_details.delivery_status','=','RELEASED')
                 ->where('costing_bulk_feature_details.combo_color','=',$colorComboId)
                 ->whereNotIn('merc_customer_order_header.order_id', DB::table('bom_so_allocation')->pluck('bom_so_allocation.order_id'))
                 ->groupBy('merc_customer_order_header.order_id','merc_customer_order_header.order_code','costing_bulk_feature_details.combo_color')
                 ->get();






    }

    public function getAssignCustomerOrders($costingId){

        return DB::table('merc_customer_order_header')
                 ->join('merc_customer_order_details','merc_customer_order_details.order_id','merc_customer_order_header.order_id')
                 ->join('merc_costing_so_combine','merc_costing_so_combine.details_id','merc_customer_order_details.details_id')
                 ->join('costing_bulk','costing_bulk.bulk_costing_id','merc_costing_so_combine.costing_id')
                 ->select('merc_customer_order_header.order_id', 'merc_customer_order_header.order_code')
                 ->where('costing_bulk.bulk_costing_id','=',$costingId)
                 ->where('merc_customer_order_details.delivery_status','=','RELEASED')
                 ->whereIn('merc_customer_order_header.order_id', DB::table('bom_so_allocation')->pluck('bom_so_allocation.order_id'))
                 ->groupBy('merc_customer_order_header.order_id','merc_customer_order_header.order_code')->get();

    }

    /*public function order_type()
		{
			 return $this->belongsTo('App\Models\Merchandising\CustomerOrderType' , 'order_type')->select(['order_type_id','order_type']);
		}*/

}
