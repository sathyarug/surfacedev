<?php

namespace App\Models\Merchandising;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class PoOrderDeliverySplit extends BaseValidator
{
    protected $table='merc_po_order_split';
    protected $primaryKey='split_id';
    const UPDATED_AT='updated_date';
    const CREATED_AT='created_date';

    protected $fillable=['split_id'];

    protected $rules=array(
        'split_id'=>'required'
    );

    public function __construct() {
        parent::__construct();
    }

    public function setDiliveryDateAttribute($value)
		{
    	$this->attributes['delivery_date'] = date('Y-m-d', strtotime($value));
    }
}
