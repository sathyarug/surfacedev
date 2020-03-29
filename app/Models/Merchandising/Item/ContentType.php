<?php
  namespace App\Models\Merchandising\Item;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class ContentType extends BaseValidator{

    protected $table = 'item_content_type';
    protected $primaryKey = 'type_code';

    protected $fillable = ['type_code','type_description'];

    protected $rules = array(
        /*'type_code' => 'required',*/
        'type_description'  => 'required'
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
