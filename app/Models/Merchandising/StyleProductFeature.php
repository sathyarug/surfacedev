<?php

namespace App\Models\Merchandising;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class StyleProductFeature extends BaseValidator
{
    protected $table='style_product_feature';
    protected $primaryKey='id';
    const UPDATED_AT='updated_date';
    const CREATED_AT='created_date';

    protected $fillable=['id','product_feature_id', 'style_id'];

    protected $rules=array(
        'product_feature_id'=>'required',
        'style_id'=>'required'

    );

    public function __construct() {
        parent::__construct();
    }
}
