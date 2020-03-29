<?php
namespace App\Models\Merchandising\Item;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class Composition extends BaseValidator{

    protected $table = 'item_content';
    protected $primaryKey = 'content_code';

    protected $fillable = ['content_code','content_description'];

    protected $rules = array(
        //'content_code' => 'required',
        'content_description'  => 'required'
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
