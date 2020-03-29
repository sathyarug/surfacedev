<?php

namespace App\Models\Merchandising;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;
use App\Libraries\UniqueIdGenerator;
use DB;

class HisBulkCosting extends BaseValidator {

    protected $table = 'his_costing_bulk';
    protected $primaryKey = 'id';
    public $timestamps = false;

    


}
