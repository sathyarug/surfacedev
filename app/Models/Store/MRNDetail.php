<?php

namespace App\Models\Store;
use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class MRNDetail extends  BaseValidator
{
    protected $table='store_mrn_detail';
    protected $primaryKey='mrn_detail_id';
    const UPDATED_AT='updated_date';
    const CREATED_AT='created_date';


    protected $fillable=['mrn_id','style_id', 'so_no', 'color', 'size', 'uom', 'item_code', 'mrn_qty', 'bal_qty'];

    protected $rules=array(

    );

    public function __construct() {
        parent::__construct();
    }


}
