<?php

namespace App\Models\Org;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class FeatureComponent extends BaseValidator
{
    protected $table='product_feature_component';
    protected $primaryKey='product_component_id';
    const UPDATED_AT='updated_date';
    const CREATED_AT='created_date';


    public function __construct() {
        parent::__construct();
    }
}
