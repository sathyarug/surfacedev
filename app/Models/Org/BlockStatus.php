<?php

namespace App\Models\Org;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class BlockStatus extends BaseValidator
{
    protected $table = 'block_status';
    protected $primaryKey = 'status_id';

    const UPDATED_AT = 'updated_date';
    const CREATED_AT = 'created_date';

    protected $fillable = [];

    public function __construct() {
        parent::__construct();
    }

}
