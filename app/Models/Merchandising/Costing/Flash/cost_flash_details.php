<?php

namespace App\Models\Merchandising\Costing\Flash;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class cost_flash_details extends Model
{
   /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'cost_flash_details';

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
    
    protected $fillable = ['costing_id', 'style_id', 'master_id', 'uom_id', 'conpc', 'unitprice', 'wastage', 'required_qty', 'total_required_qty', 'total_value', 'supplier_id', 'status'];
    
    public function getCostingLineDetails($costingId){
        
        return DB::table('cost_flash_details')
                 ->join('item_master','item_master.master_id','cost_flash_details.master_id')
                 ->join('org_uom','org_uom.uom_id','cost_flash_details.uom_id')
                 ->leftJoin('org_supplier','org_supplier.supplier_id','=','cost_flash_details.supplier_id')  
                 ->select('cost_flash_details.*','org_supplier.supplier_name','item_master.master_description','org_uom.uom_description')
                 ->where('cost_flash_details.costing_id','=',$costingId)
                 ->where('cost_flash_details.status','=',1)->get();
        
        
    }
    
    public function getCostingLineItems($costingId, $itemCode){
       return DB::table('cost_flash_details')
              ->join('item_master','item_master.master_id', 'cost_flash_details.master_id')
              ->join('item_subcategory', 'item_subcategory.subcategory_id','item_master.subcategory_id')  
              ->select('cost_flash_details.*','item_master.subcategory_id', 'item_subcategory.category_id')
              ->where('cost_flash_details.costing_id','=',$costingId)
              ->where('cost_flash_details.master_id','=',$itemCode)->get();
        
    }
    
    public function supplier()
    {
       return $this->belongsTo('App\Models\Org\Supplier' , 'supplier_id');
    }
}
