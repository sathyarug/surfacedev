<?php

namespace App\Models\Merchandising\Item;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use App\BaseValidator;

class AssignProperty extends BaseValidator
{
    protected $table = 'item_property_assign';

    protected $primaryKey = 'property_assign_id';

    protected $fillable = ['property_assign_id','property_id','subcategory_id','status', 'sequence_no'];

    public function __construct()
    {
        parent::__construct();      
    }

}
