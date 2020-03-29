<?php
namespace APP\Models\stores;
use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class TransferLocationUpdate extends BaseValidator
{
  protected $table='store_stock';
  protected $primaryKey='id';
  const UPDATED_AT='updated_date';
  const CREATED_AT='created_date';



      /*protected $rules=array(
          'item_code'=>'required'
      );*/

      public function __construct() {
          parent::__construct();
      }



}
