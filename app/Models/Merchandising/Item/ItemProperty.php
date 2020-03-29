<?php

namespace App\Models\Merchandising\Item;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use App\BaseValidator;

class itemproperty extends BaseValidator
{

    protected $table = 'item_property';
    protected $primaryKey = 'property_id';

    protected $fillable = ['property_id','property_name','status'];

    public function load_assign_properties($sub_category){
        return DB::table('item_property')
        ->join('item_property_assign','item_property_assign.property_id','=','item_property.property_id')
        ->select('item_property.property_id','item_property.property_name')
        ->where('item_property_assign.subcategory_id','=', $sub_category)
        ->where('item_property_assign.status','<>', 0)
        ->orderBy('sequence_no')->get();
    }

    protected $rules = array(
        'property_name'  => 'required'
    );

    public function LoadUnAssignPropertiesBySubCat($result){

       $subcatCode = $result->subcategory_code;

        return DB::table('item_property')->select('item_property.property_id','item_property.property_name')->whereNotIn('item_property.property_id',function($q) use ($subcatCode){
           $q->select('property_id')->from('item_property_assign')->where('subcategory_id',$subcatCode);
       })->get();
    }

}
