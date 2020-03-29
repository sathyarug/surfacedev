<?php
namespace App\Models\Merchandising;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;


class ProductFeatureComponent extends BaseValidator {

    protected $table = 'product_feature_component';
    protected $primaryKey = 'feature_component_id';

    const UPDATED_AT = 'updated_date';
    const CREATED_AT = 'created_date';

    protected $fillable = ['product_feature_id','product_component_id','product_silhouette_id'];

    protected $rules = array(
        'product_feature_id' => 'required',
        'product_component_id' => 'required',
        'product_silhouette_id' => 'required'

    );



    public function __construct() {
        parent::__construct();
        $this->attributes = array(
            'updated_by' => 2//Session::get("user_id")
        );
    }


}
