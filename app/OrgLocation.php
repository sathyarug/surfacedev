<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrgLocation extends Model
{
    protected $table = 'org_location';
    protected $primaryKey = 'loc_id';
    protected $fillable = [
        'loc_code','company_code','loc_name','loc_type','loc_address','loc_phone','loc_fax','time_zone','default_currency'
    ];


}
