<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class PriceVariance extends BaseValidator
{
    protected $table = 'fin_price_variance';
    protected $primaryKey = 'price_variance_id';
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';

    protected $fillable = ['item_id','purchase_price','standard_price','shop_order_id'];


  public function __construct()
    {
        parent::__construct();
        /*$this->attributes = array(
            'updated_by' => 2//Session::get("user_id")
        );*/
    }


}
