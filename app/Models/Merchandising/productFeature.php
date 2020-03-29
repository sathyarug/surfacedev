<?php
/**
 * Created by PhpStorm.
 * User: shanilad
 * Date: 9/4/2018
 * Time: 11:46 AM
 */

namespace App\Models\Merchandising;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;


class productFeature extends BaseValidator {

    protected $table = 'product_feature';
    protected $primaryKey = 'product_feature_id';

    const UPDATED_AT = 'updated_date';
    const CREATED_AT = 'created_date';

    protected $fillable = ['product_feature_description'];

    protected $rules = array(
        'product_feature_description' => 'required'

    );



    public function __construct() {
        parent::__construct();
        $this->attributes = array(
            'updated_by' => 2//Session::get("user_id")
        );
    }


}