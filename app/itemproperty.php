<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class itemproperty extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'item_property';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'property_id';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['property_id','property_name','status'];

    public function LoadAssignProperties($request){

        $subcatcode = $request->subcategory_code;

        return DB::table('item_property')->join('item_property_assign','item_property_assign.property_id','=','item_property.property_id')->select('item_property.property_id','item_property.property_name')->where('item_property_assign.subcategory_id','=',$subcatcode)->orderBy('sequence_no')->get();

    }

    public function LoadUnAssignPropertiesBySubCat($result){

       $subcatCode = $result->subcategory_code;

        return DB::table('item_property')->select('item_property.property_id','item_property.property_name')->whereNotIn('item_property.property_id',function($q) use ($subcatCode){
           $q->select('property_id')->from('item_property_assign')->where('subcategory_id',$subcatCode);
       })->get();
    }

}
