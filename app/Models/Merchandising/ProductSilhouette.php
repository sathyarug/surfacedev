<?php
/**
 * Created by PhpStorm.
 * User: shanilad
 * Date: 9/6/2018
 * Time: 3:03 PM
 */

namespace App\Models\Merchandising;
use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class ProductSilhouette extends BaseValidator {

    protected $table = 'product_silhouette';
    protected $primaryKey = 'product_silhouette_id';

    const UPDATED_AT = 'updated_date';
    const CREATED_AT = 'created_date';

    protected $fillable = ['product_silhouette_description'];

    protected $rules = array(
        'product_silhouette_description' => 'required'

    );



    public function __construct() {
        parent::__construct();
        $this->attributes = array(
            'updated_by' => 2//Session::get("user_id")
        );
    }

}