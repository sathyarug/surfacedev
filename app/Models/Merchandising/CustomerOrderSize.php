<?php

namespace App\Models\Merchandising;

use Illuminate\Database\Eloquent\Model;

use App\BaseValidator;
use App\Libraries\UniqueIdGenerator;

class CustomerOrderSize extends BaseValidator
{
    protected $table='merc_customer_order_size';
    protected $primaryKey='id';
    const UPDATED_AT='updated_date';
    const CREATED_AT='created_date';

    protected $fillable=['details_id','size_id','order_qty','planned_qty'];

    protected $rules=array(
        'details_id'=>'required',
        'size_id'=>'required',
        'order_qty' => 'required',
        'planned_qty' => 'required'

    );

    public function __construct() {
        parent::__construct();
    }

    //default currency of the company
		public function size()
		{
			 return $this->belongsTo('App\Models\Org\Size' , 'size_id')->select(['size_id','size_name']);
		}



}
