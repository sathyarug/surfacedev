<?php

namespace App\Models\stores;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class StoRollDescription extends BaseValidator
{
    protected $table='sto_roll_description';
    protected $primaryKey='id';
    const UPDATED_AT='updated_date';
    const CREATED_AT='created_date';

 protected $fillable=['item_code','lot_no','roll_no','qty_yardage','comment'];

    protected $rules=array(
        'item_code'=>'required'
    );

    public function __construct() {
        parent::__construct();
    }
}
