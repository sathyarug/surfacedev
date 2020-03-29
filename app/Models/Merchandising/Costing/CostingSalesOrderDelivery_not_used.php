<?php

namespace App\Models\Merchandising\Costing;

use Illuminate\Database\Eloquent\Model;

use App\BaseValidator;

class CostingSalesOrderDelivery extends BaseValidator {

    protected $table = 'costing_sales_order_deliveries';
    protected $primaryKey = null;
    public $incrementing = false;

    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';

    protected $fillable = ['costing_id', 'delivery_id'];

    protected $rules = array(
        'costing_id' => 'required',
        'delivery_id' => 'required'
    );

}
