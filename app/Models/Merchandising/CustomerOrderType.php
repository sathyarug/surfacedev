<?php

namespace App\Models\Merchandising;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class CustomerOrderType extends BaseValidator
{
    protected $table='merc_customer_order_type';
    protected $primaryKey='order_type_id';
    //const UPDATED_AT='updated_date';
    //const CREATED_AT='created_date';

    protected $fillable=['order_type','order_type_id'];

    /*protected $rules=array(
        'division_code'=>'required',
        'division_description'=>'required'
    );*/

    public function __construct() {
        parent::__construct();

    }
}
