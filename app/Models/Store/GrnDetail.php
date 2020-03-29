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



class GrnDetail extends BaseValidator
{
    protected $table='store_grn_detail';
    protected $primaryKey='grn_detail_id';
    //public $timestamps = false;
    const UPDATED_AT='updated_date';
    const CREATED_AT='created_date';

    protected $fillable=['grn_id','grn_number','po_number','po_details_id', 'inv_number', 'maximum_tolarance','original_bal_qty','sup_id', 'note', 'main_store', 'sub_store', 'location','excess_qty','status'];

    protected $rules=array(
        ////'color_code'=>'required',
        //'color_name'=>'required'
    );

    public function __construct() {
        parent::__construct();
    }

    public static function getGrnLineDataWithBins($lineId){
        $result = self::join('store_grn_header', 'store_grn_header.grn_id',   '=', 'store_grn_detail.grn_id')
            ->join('store_stock_transaction', 'store_stock_transaction.doc_num', '=', 'store_grn_header.grn_id')
            ->join('org_store_bin', 'org_store_bin.store_bin_id', '=', 'store_stock_transaction.bin')
            ->where('store_grn_detail.grn_detail', '=', $lineId)
            ->select('store_grn_header.grn_id', 'store_stock_transaction.qty', 'org_store_bin.store_bin_name', 'org_store_bin.store_bin_id')
            //->groupBy('store_grn_detail.id')
            ->get();
        return $result;
    }

}
