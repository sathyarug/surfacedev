<?php
namespace APP\Models\store;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\BaseValidator;
use App\Libraries\UniqueIdGenerator;
class TrimPacking extends BaseValidator{

  protected $table='store_trim_packing_detail';
  protected $primaryKey='trim_packing_id';
  const UPDATED_AT='updated_date';
  const CREATED_AT='created_date';

  protected $fillable=['roll_plan_id','invoice_no','grn_detail_id','lot_no','batch_no','received_qty','bin','width','shade','comment','barcode'];

  public function __construct() {
      parent::__construct();
  }

  protected $rules=array(
    'invoiceNo'=>'required',
  );

}
