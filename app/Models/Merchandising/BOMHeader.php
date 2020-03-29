<?php

namespace App\Models\Merchandising;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BOMHeader extends Model
{

    protected $table = 'bom_header';
    protected $primaryKey = 'bom_id';
    const UPDATED_AT='updated_date';
    const CREATED_AT='created_date';

    protected $fillable = ['bom_id', 'costing_id'];


    //Relationships.............................................................

    public function finish_good()
    {
        return $this->belongsTo('App\Models\Merchandising\Item\Item', 'fng_id')->select(['master_id', 'master_code', 'master_description']);
    }

    public function country()
    {
        return $this->belongsTo('App\Models\Org\Country', 'country_id')->select(['country_id', 'country_code', 'country_description']);
    }





    public function getBOMOrderQty($bomID){

        return DB::table('merc_customer_order_details')->select(DB::raw("SUM(order_qty) AS Order_Qty"))
              ->join('bom_so_allocation','bom_so_allocation.order_id','merc_customer_order_details.order_id')
              ->join('bom_header','bom_header.bom_id','bom_so_allocation.bom_id')
              ->where('bom_header.bom_id','=',$bomID)
              ->where('delivery_status','RELEASED')->get();

    }

    public function getColorCombpoByCosting($costingId){

        return DB::table('costing_bulk_feature_details')
                ->join('org_color','org_color.color_id','=','costing_bulk_feature_details.combo_color')
                ->select('org_color.color_id','org_color.color_name')
                ->where('costing_bulk_feature_details.bulkheader_id',$costingId)
                ->groupBy('org_color.color_id','org_color.color_name')
                ->get();
    }

    //other functions...........................................................

    public function calculate_epm($fob, $total_rm_cost, $smv){
      $epm = ($smv == 0) ? 0 : ($fob - $total_rm_cost) / $smv; //(fob - rm cost) / smv
      return round($epm, 4, PHP_ROUND_HALF_UP ); //round and return
    }

    public function calculate_np($fob, $total_cost){
      $np = ($total_cost == 0) ? 0 : ($total_cost - $fob) / $total_cost; //(total cost - fob) / total cost
      return round($np, 4, PHP_ROUND_HALF_UP ); //round and return
    }
}
