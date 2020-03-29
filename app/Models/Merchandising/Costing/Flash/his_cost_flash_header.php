<?php

namespace App\Models\Merchandising\Costing\Flash;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class his_cost_flash_header extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'his_cost_flash_header';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'history_id';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['history_id','costing_id', 'style_id', 'order_qty', 'order_status', 'approval_no', 'revision_id', 'order_smv', 'order_fob', 'order_eff', 'revised_by', 'revised_on', 'season_id'];
   
}

