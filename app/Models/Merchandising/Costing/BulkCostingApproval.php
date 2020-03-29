<?php

namespace App\Models\Merchandising\Costing;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;
use App\Libraries\UniqueIdGenerator;
use DB;

class BulkCostingApproval extends model {

    protected $table = 'costing_approval';
    protected $primaryKey = 'id';

    public $timestamps = false;



}
