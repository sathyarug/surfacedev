<?php

namespace App\Models\Merchandising;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use App\BaseValidator;
use App\Libraries\UniqueIdGenerator;

class HisBulkCostingDetails extends BaseValidator {

    protected $table = 'his_costing_bulk_details';
 	protected $primaryKey = 'id';
    public $timestamps = false;
   
   

}
