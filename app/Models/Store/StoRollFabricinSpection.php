<?php

namespace App\Models\Store;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class StoRollFabricinSpection extends BaseValidator
{
    protected $table='sto_roll_fabricin_spection';
    protected $primaryKey='id';
    const UPDATED_AT='updated_date';
    const CREATED_AT='created_date';

//    protected $fillable=['store_id','substore_id','store_bin_name','store_bin_description'];


}
