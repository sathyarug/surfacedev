<?php
namespace APP\Models\stores;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\BaseValidator;
use App\Libraries\UniqueIdGenerator;
class RollPlan extends BaseValidator{

  protected $table='store_roll_plan';
  protected $primaryKey='roll_plan_id';
  const UPDATED_AT='updated_date';
  const CREATED_AT='created_date';

  protected $fillable=['roll_plan_id','invoice_no','grn_detail_id','lot_no','batch_no','qty','received_qty','bin','width','shade','comment','barcode'];

  public function __construct() {
      parent::__construct();
  }

  protected $rules=array(
    'invoiceNo'=>'required',
  );

}
