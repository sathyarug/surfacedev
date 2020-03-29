<?php

namespace App\Models\Merchandising;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;
use App\Libraries\UniqueIdGenerator;

class HisBulkCostingFeatureDetails extends BaseValidator {

    protected $table = 'his_costing_bulk_feature_details';
    protected $primaryKey = 'id';
    public $timestamps = false;

}
