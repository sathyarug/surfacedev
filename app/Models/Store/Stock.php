<?php


namespace App\Models\Store;

use Illuminate\Database\Eloquent\Model;

use App\BaseValidator;



class Stock extends BaseValidator
{
    protected $table='store_stock';
    protected $primaryKey='id';
    //public $timestamps = false;
    const UPDATED_AT='updated_date';
    const CREATED_AT='created_date';

    protected $fillable=['item_code','location', 'customer_po_id','store', 'sub_store', 'uom', 'weighted_average_price', 'inv_qty', 'tolerance_qty', 'total_qty','material_code'
    ];

    protected $rules=array(
        ////'color_code'=>'required',
        //'color_name'=>'required'
    );

    public function __construct() {
        parent::__construct();
    }

    public static function getBinStock($id){
        $podata = Stock::where('store_stock.bin', $id)
            ->join("item_master AS i", "i.master_id", "=", "store_stock.material_code")
            ->join("org_color AS c", "c.color_id", "=", "store_stock.color")
            ->join("org_size AS s", "s.size_id", "=", "store_stock.size")
            ->join("org_uom AS u", "u.uom_id", "=", "store_stock.uom")
            ->join("style_creation AS y", "y.style_id", "=", "store_stock.style_id")
            ->select( "c.color_name","s.size_name", "u.uom_code", "i.master_description", "i.master_id", "store_stock.total_qty", "y.style_no", "c.color_id", "s.size_id", "i.master_id", "u.uom_id", "y.style_id")
            ->get()
            ->toArray();

        return $podata;
    }

    public static function GetStockRecord($obj){
        $users = DB::table(self::table)
            ->select(DB::raw('count(*) as user_count, status'))
            ->where('status', '<>', 1)
            ->groupBy('status')
            ->get();
    }

    public static function getCurrentStockOfLoc(){
        $stockData = self::where('store_stock.location', 2)
            ->join("item_master AS i", "i.master_id", "=", "store_stock.material_code")
            ->join("org_color AS c", "c.color_id", "=", "store_stock.color")
            ->join("org_size AS s", "s.size_id", "=", "store_stock.size")
            ->join("org_uom AS u", "u.uom_id", "=", "store_stock.uom")
            ->join("style_creation AS y", "y.style_id", "=", "store_stock.style_id")
            ->join("merc_customer_order_details AS d", "d.details_id", "=", "store_stock.customer_po_id")
            ->groupBy('store_stock.style_id', 'store_stock.customer_po_id', 'store_stock.size', 'store_stock.color', 'store_stock.material_code', 'store_stock.style_id')
           // ->select("i.master_id", "i.master_description",  "c.color_name","s.size_name", "u.uom_code", "d.po_no", "y.style_no")
            ->selectRaw('i.master_id, i.master_description, c.color_name, s.size_name, u.uom_code, d.po_no, y.style_no,  sum(store_stock.total_qty) as qty')
            ->get()
            ->toArray();

        return $stockData;
    }

    public static function getStockForReturnToSup(){

    }

}
