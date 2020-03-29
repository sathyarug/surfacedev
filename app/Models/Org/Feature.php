<?php

namespace App\Models\Org;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class Feature extends BaseValidator
{
    protected $table='product_feature';
    protected $primaryKey='product_feature_id';
    const UPDATED_AT='updated_date';
    const CREATED_AT='created_date';

    protected $fillable=['product_feature_description','product_feature_id'];

    protected $rules=array(
        'product_feature_description'=>'required'
    );

    public function __construct() {
        parent::__construct();
    }
}
