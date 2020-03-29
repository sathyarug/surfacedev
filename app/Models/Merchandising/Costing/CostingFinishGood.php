<?php

namespace App\Models\Merchandising\Costing;

use Illuminate\Database\Eloquent\Model;
use App\Libraries\UniqueIdGenerator;
use DB;

use App\BaseValidator;

class CostingFinishGood extends BaseValidator {

    protected $table = 'costing_finish_goods';
    protected $primaryKey = 'fg_id';

    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';

    protected $fillable = ['pack_no', 'combo_color_id', 'feature_id'];

    protected $rules = array(
      /*  'style_id' => 'required',
        'bom_stage_id' => 'required',
        'season_id' => 'required',      */
    );


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
