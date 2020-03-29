<?php

namespace App\Models\Org;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class LocationType extends BaseValidator
{
    protected $table = 'type_of_location';
    protected $primaryKey = 'type_loc_id';
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';

    protected $fillable = ['type_location'];

    protected $rules = array(
        'type_location' => 'required'
    );

    public function __construct()
    {
        parent::__construct();    
    }


}
