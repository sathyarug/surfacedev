<?php

namespace App\Models\Merchandising;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class ShopOrderDetailsHistory extends BaseValidator
{
    protected $table = 'merc_shop_order_detail_history';
    protected $primaryKey = 'id';

    const UPDATED_AT = 'updated_date';
    const CREATED_AT = 'created_date';


    /*protected $rules = array(

    );*/

    public function __construct() {
        parent::__construct();
    }




}
