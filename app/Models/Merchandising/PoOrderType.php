<?php

namespace App\Models\Merchandising;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class PoOrderType extends BaseValidator
{
    protected $table='po_order_type';
    protected $primaryKey='po_type_id';
    const UPDATED_AT='updated_date';
    const CREATED_AT='created_date';

//    protected $fillable=[];

    protected $rules=array(
        'po_status_name'=>'required'
    );

    public function __construct() {
        parent::__construct();
    }
}
