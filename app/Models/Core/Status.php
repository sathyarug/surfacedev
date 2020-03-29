<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class Status extends BaseValidator
{
    protected $table='core_status';
    protected $primaryKey='status';
    protected $keyType = 'varchar';
    //const UPDATED_AT='updated_date';
    //const CREATED_AT='created_date';

    //protected $fillable=['color_code','color_name','color_id'];

    /*protected $rules=array(
        'color_code'=>'required',
        'color_name'=>'required'
    );*/

    public function __construct() {
        parent::__construct();
    }
}
