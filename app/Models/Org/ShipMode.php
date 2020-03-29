<?php

namespace App\Models\Org;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class ShipMode extends BaseValidator {

    protected $table = 'org_ship_mode';
    protected $primaryKey = 'ship_mode';
    protected $keyType = 'varchar';

    ///onst UPDATED_AT = 'updated_date';
    //const CREATED_AT = 'created_date';

    //protected $fillable = ['season_code', 'season_name', 'season_id'];
    /*protected $rules = array(
        'season_code' => 'required',
        'season_name' => 'required'
    );*/

    public function __construct() {
        parent::__construct();      
    }

}
