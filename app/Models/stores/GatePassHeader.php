<?php
namespace APP\Models\stores;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\BaseValidator;
use App\Libraries\UniqueIdGenerator;
class GatePassHeader extends BaseValidator{

  protected $table='store_gate_pass_header';
  protected $primaryKey='gate_pass_id';
  const UPDATED_AT='updated_date';
  const CREATED_AT='created_date';

  //protected $fillable=['id','transaction_id','transfer_location','receiver_location','status'];

  public function __construct() {
      parent::__construct();
  }
  /*public static function boot(){

    /*static::creating(function($model){

      $user=auth()->user();
      $gate_pass_no=UniqueIdGenerator::generateUniqueId('GATE_PASS',$user->location);
      $model->gate_pass_no=$gate_pass_no;
      $model->created_by=$user->user_id;

    });

      parent::boot();

  }*/

}
