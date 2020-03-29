<?php

namespace App\Models\Manufacturing;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\BaseValidator;

class ManProdOrderDetails extends BaseValidator
{
    protected $table = 'man_prod_order_details';
    protected $primaryKey = 'details_id';
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';

//    protected $fillable = ['type_location'];

    protected $rules = array(
        'prod_id' => 'details_id'
    );

    public function __construct()
    {
        parent::__construct();    
    }


}
