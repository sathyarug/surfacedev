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


class ProductType extends BaseValidator {

    protected $table = 'product_type';
    protected $primaryKey = 'pack_type_id';

    const UPDATED_AT = 'updated_date';
    const CREATED_AT = 'created_date';

    protected $fillable = ['pack_type_description'];

    protected $rules = array(
        'pack_type_description' => 'required'

    );



    public function __construct() {
        parent::__construct();
        $this->attributes = array(
            'updated_by' => 2//Session::get("user_id")
        );
    }


}
