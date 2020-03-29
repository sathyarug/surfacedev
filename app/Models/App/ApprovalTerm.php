<?php

namespace App\Models\App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ApprovalTerm extends Model
{
    protected $table='app_approval_terms';
    protected $primaryKey='term_id';
    public $timestamps = false;

    protected $rules = array(
        'process' => 'required'
    );

    public function __construct()  {
        parent::__construct();
    }

    //custom functions .........................................................
    public function execute_query($query){
      $result = DB::select($query);
      if($result == null || $result['term_result'] == 0){
        return false;
      }
      else if($result['term_result'] == 1){
        return true;
      }
    }

}
