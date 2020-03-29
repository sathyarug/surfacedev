<?php

namespace App\Models\Merchandising\Costing\Flash;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class his_cost_flash_details extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'his_cost_flash_details';

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
    protected $fillable = ['history_id','costing_id', 'style_id', 'master_id', 'uom_id', 'conpc', 'unitprice', 'wastage', 'required_qty', 'total_required_qty', 'total_value', 'supplier_id'];
   
}
