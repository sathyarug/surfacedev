<?php

namespace App\Models\Store;
use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class ReturnToStoreDetails extends BaseValidator
{
    protected $table='store_return_to_store_detail';
    protected $primaryKey='return_detail_id';

    const UPDATED_AT = 'updated_date';
    const CREATED_AT = 'created_date';
    
    protected $fillable=[
        'return_detail_id',
        'return_id',
        'item_id',
        'inv_uom',
        'request_uom',
        'issue_qty',
        'return_qty',
        'status',
        'location_id',
        'store_id',
        'sub_store_id',
        'bin',
        'roll_box',
        'batch_no',
        'shade',
        'item_detail_id',
        'comments'
    ];


    //Validation functions......................................................
    /**
    *unique:table,column,except,idColumn
    *The field under validation must not exist within the given database table
    */
    protected function getValidationRules($data) {
      return [
        'return_id' => 'required',
        'item_id' => 'required',
        'inv_uom' => 'required',
        'issue_qty' => 'required',
        'return_qty' => 'required',
        'status' => 'required',
        'location_id' => 'required',
        'store_id' => 'required',
        'sub_store_id' => 'required',
        'bin' => 'required',
        'roll_box' => 'required',
        'batch_no' => 'required',
        'shade' => 'required',
        'item_detail_id' => 'required'
      ];
    }


    public function __construct()
    {
    	parent::__construct();
    }


}
