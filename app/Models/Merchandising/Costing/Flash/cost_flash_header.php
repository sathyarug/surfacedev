<?php

namespace App\Models\Merchandising\Costing\Flash;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class cost_flash_header extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'cost_flash_header';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'costing_id';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['costing_id', 'style_id', 'order_qty', 'order_status', 'approval_no', 'order_smv', 'order_fob', 'order_eff', 'revised_by', 'revised_on', 'season_id','confirm_by', 'confirm_at'];
    
    public function ListCostingId($style_id){
        
        return DB::table('cost_flash_header')->select(DB::raw("costing_id, LPAD(costing_id,6,'0') AS CostingNumber"))->where('style_id','=',$style_id)->get();
    }
}
