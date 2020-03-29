<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrgShipmentMode extends Model
{
    protected $table='org_ship_mode';
    protected $primaryKey='shipment_mode_id';
    const UPDATED_AT='updated_date';
    const CREATED_AT='created_date';

}
