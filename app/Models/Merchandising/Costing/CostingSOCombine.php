<?php

namespace App\Models\Merchandising\Costing;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class CostingSOCombine extends Model
{
    protected $table='merc_costing_so_combine';
    protected $primaryKey='id';
    const UPDATED_AT='updated_date';
    const CREATED_AT='created_date';

//    protected $fillable=['po_id'];

    protected $rules=array(
        'po_id'=>'required'
    );

    public function __construct() {
        parent::__construct();
    }

}
