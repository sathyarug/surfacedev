<?php

namespace App\Models\Merchandising\Item;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class PropertyValueAssign extends BaseValidator{

    protected $table = 'item_property_assign_value';
    protected $primaryKey = 'property_value_id';

    protected $fillable = ['property_value_id','property_id','assign_value','status'];

    protected $rules = array(
        /*'property_value_id' => 'required',*/
        'property_id'  => 'required',
        'assign_value' => 'required'
    );

    public function __construct()
    {
        parent::__construct();
        $this->attributes = array(
            'updated_by' => 2//Session::get("user_id")
        );
    }
}

?>
