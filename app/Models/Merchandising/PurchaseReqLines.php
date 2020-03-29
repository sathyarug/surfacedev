<?php

namespace App\Models\Merchandising;
use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class PurchaseReqLines extends BaseValidator
{
    protected $table='merc_purchase_req_lines';
    protected $primaryKey='prl_id';
    const UPDATED_AT='updated_date';
    const CREATED_AT='created_date';

    protected $fillable=['bom_id','cpo_no','item_code','item_desc','item_color','color_name','item_size','size_name','uom_id','uom_code','supplier_id','supplier_name','unit_price','total_qty','moq','mcq','order_id','bal_order','po_qty','status'];

    protected $rules=array(
        'bom_id'=>'required'
    );



    public function __construct() {
        parent::__construct();
        $this->attributes = array(
            'updated_by' => 2//Session::get("user_id")
        );
    }
}
