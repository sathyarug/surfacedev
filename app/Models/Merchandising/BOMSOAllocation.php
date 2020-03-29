<?php

namespace App\Models\Merchandising;

use Illuminate\Database\Eloquent\Model;

class BOMSOAllocation extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'bom_so_allocation';

    /**
    * The database primary key value.
    *
    * @var string
    */
    //protected $primaryKey = ['costing_id','style_id','master_id'];
    protected $primaryKey = 'costing_id';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    
    public $timestamps = false;

    protected $fillable = ['costing_id', 'order_id', 'bom_id'];
}
