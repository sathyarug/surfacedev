<?php
/**
 * Created by PhpStorm.
 * User: sankap
 * Date: 11/4/2018
 * Time: 10:47 PM
 */

namespace App\Models\Store;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;



class GrnHeader extends BaseValidator
{
    protected $table='store_grn_header';
    protected $primaryKey='grn_id';
    //public $timestamps = false;
    const UPDATED_AT='updated_date';
    const CREATED_AT='created_date';

    protected $fillable=['grn_number','po_number','batch_no', 'inv_number', 'sup_id', 'note'];

    protected $rules=array(
        ////'color_code'=>'required',
        //'color_name'=>'required'
    );

    public function __construct() {
        parent::__construct();
    }

    public function grnDetails(){
        return $this->hasMany('App\Models\Store\GrnDetail', 'transfer_id', 'transfer_id');
    }

    public static function getGrnLineData($request){
        $grn = GrnHeader::where('store_grn_header.grn_id', $request->id)
            ->join("store_grn_detail AS d", "d.grn_id", "=", "store_grn_header.grn_id")
            ->join("merc_po_order_header AS m", "m.po_id", "=", "store_grn_header.po_number")
            ->join("merc_customer_order_header AS o", "o.order_code", "=", "d.sc_no")
            ->join("item_master AS i", "i.master_id", "=", "d.item_code")
            ->join("org_color AS c", "c.color_id", "=", "d.color")
            ->join("org_size AS s", "s.size_id", "=", "d.size")
            ->join("org_uom AS u", "u.uom_id", "=", "d.uom")
            ->join("cust_customer AS cu", "cu.customer_id", "=", "o.order_customer")
            ->select("store_grn_header.grn_id","store_grn_header.grn_number", "d.id", "d.item_code","cu.customer_name", "s.size_name", "u.uom_description", "d.po_qty", "d.grn_qty", "d.bal_qty", "d.id", "d.sc_no", "i.master_description", "c.color_name")
            ->get()
            ->toArray();

        return $grn;
    }


}
