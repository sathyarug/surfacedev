<?php

namespace App\Models\Merchandising;

//use App\Http\Controllers\Merchandising\PurchaseOrder;
use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class PoOrderDetailsRevision extends BaseValidator
{
    protected $table='merc_po_order_details_revision';
    protected $primaryKey='id';
    const UPDATED_AT='updated_date';
    const CREATED_AT='created_date';

    protected $fillable=['po_no','item_code','unit_price','uom','req_qty','deli_date','remarks','status','tot_qty','sc_no','style','colour','size'];


    protected $rules=array(
        'po_no'=>'required'
    );



    public function __construct() {
        parent::__construct();
    }

    public function setDiliveryDateAttribute($value)
		{
    	$this->attributes['deli_date'] = date('Y-m-d', strtotime($value));
    }

    //public function purchaseOrder(){
    //    return $this->belongsTo(PurchaseOrder::class);
    //}
}
