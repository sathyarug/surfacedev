<?php

namespace App\Models\stores;
use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class StoreScarpDetails extends Model
{
    protected $table='store_inv_scarp_details';
    protected $primaryKey='detail_id';
    
    protected $fillable=['report_no','status','style','shop_order_id','master_id','bin_no','roll_box_no','batch','inv_qty','scarp_qty','inv_value','comments'];

    protected $rules=array();

    public $timestamps = false;

    public function __construct() {
        parent::__construct();
    }


}
