<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UsrLocMap extends Model
{
    protected $table = 'usr_loc_map';
    protected $primaryKey = 'id';
    protected $fillable = [
        'user_id', 'loc_code'
    ];


}
