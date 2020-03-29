<?php
namespace App\Models\Merchandising;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;


class ProductComponent extends BaseValidator {

    protected $table = 'product_component';
    protected $primaryKey = 'product_component_id';

    const UPDATED_AT = 'updated_date';
    const CREATED_AT = 'created_date';

    protected $fillable = ['product_component_description'];

    protected $rules = array(
        'product_component_description' => 'required'

    );



    public function __construct() {
        parent::__construct();
        $this->attributes = array(
            'updated_by' => 2//Session::get("user_id")
        );
    }


}
