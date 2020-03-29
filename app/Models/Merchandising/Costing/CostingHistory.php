<?php

namespace App\Models\Merchandising\Costing;

use Illuminate\Database\Eloquent\Model;
use App\Libraries\UniqueIdGenerator;
use DB;
use App\BaseValidator;

class CostingHistory extends BaseValidator {

    protected $table = 'costing_history';
    protected $primaryKey = 'id';
  
}
