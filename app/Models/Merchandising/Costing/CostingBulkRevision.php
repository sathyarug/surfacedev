<?php

namespace App\Models\Merchandising\Costing;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use App\BaseValidator;
use App\Libraries\UniqueIdGenerator;

class CostingBulkRevision extends BaseValidator {

    protected $table = 'costing_bulk_revision';
    protected $primaryKey = 'id';

    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';

    protected $fillable = [];




}
