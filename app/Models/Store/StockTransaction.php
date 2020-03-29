<?php


namespace App\Models\Store;

use Illuminate\Database\Eloquent\Model;

use App\BaseValidator;



class StockTransaction extends Model
{
    protected $table='store_stock_transaction';
    protected $primaryKey='transaction_id';
    //public $timestamps = false;
    //const UPDATED_AT='updated_date';
    const CREATED_AT='created_date';
    const UPDATED_AT = null;

    protected $fillable=['doc_num','doc_type','combine_id', 'item_code','customer_po_id' ,'so','main_store', 'sub_store', 'location', 'bin', 'size', 'color','qty', 'uom', 'status'];

    protected $rules=array(
        ////'color_code'=>'required',
        //'color_name'=>'required'
    );

    public function __construct() {
        parent::__construct();
    }

}
