<?php

namespace App\Models\IE;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class ProductCMP extends BaseValidator
{
 protected $table = 'ie_product_cpm';
    protected $primaryKey = 'cmp_id';

    
    public function __construct() {
        parent::__construct();
        $this->attributes = array(
            'updated_by' => 2//Session::get("user_id")
        );
    }
}
