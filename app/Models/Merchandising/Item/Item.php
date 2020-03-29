<?php

namespace App\Models\Merchandising\Item;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use App\BaseValidator;

class Item extends BaseValidator
{
  public function __construct()
  {
    //add functions names to 'except' paramert to skip authentication
    parent::__construct();
  }

    protected $table = 'item_master';
    protected $primaryKey = 'master_id';
    protected $fillable = ['master_id', 'subcategory_id', 'master_code', 'master_description', 'uom_id', 'status', 'category_id', 'supplier_reference','gsm','width'];

    protected $rules = array(
        'subcategory_id'  => 'required',
        'master_description'  => 'required'
    );

    //Accessors & Mutators......................................................

    public function setSupplierReferenceAttribute($value) {
        $this->attributes['supplier_reference'] = strtoupper($value);
    }

    //Relationships.............................................................

    public function uoms() {
        return $this->belongsToMany('App\Models\Org\UOM', 'item_uom', 'master_id', 'uom_id');
    }

    public function item_properties() {
        return $this->belongsToMany('App\Models\Merchandising\Item\ItemProperty', 'item_property_data', 'master_id', 'property_id')
        ->withPivot('property_value_id', 'other_data', 'other_data_type');;
    }

    public function LoadItems(){
        return DB::table('item_master')
           ->join('item_subcategory','item_subcategory.subcategory_id','=','item_master.subcategory_id')
           ->join('item_category','item_category.category_id','=','item_subcategory.category_id')
           ->select('item_master.master_id','item_category.category_name','item_master.master_description','item_master.status')->get();
    }

}
