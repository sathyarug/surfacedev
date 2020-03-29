<?php

namespace App\Models\Merchandising;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class ShopOrderDetail extends BaseValidator
{
    protected $table = 'merc_shop_order_detail';
    protected $primaryKey = 'shop_order_detail_id';

    const UPDATED_AT = 'updated_date';
    const CREATED_AT = 'created_date';


    /*protected $rules = array(

    );*/

    public function __construct() {
        parent::__construct();
    }




}
