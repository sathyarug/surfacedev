<?php
namespace App\Models\Merchandising;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;


class BuyMaster extends BaseValidator{


  protected $table = 'buy_master';
  protected $primaryKey = 'buy_id';

  const UPDATED_AT = 'updated_date';
  const CREATED_AT = 'created_date';

  protected $fillable = ['buy_name'];

  // protected $rules=array(
  //     'buy_name'=>'required'
  // );

  protected function getValidationRules($data) {
    return [
        'buy_name' => [
          'required',
          'unique:buy_master,buy_name,'.$data['buy_id'].',buy_id',
        ],
    ];
  }

  public function __construct() {
        parent::__construct();
  }




}
