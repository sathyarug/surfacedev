<?php

namespace App;


use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable;

class usr_login extends Model implements Authenticatable
{
    use \Illuminate\Auth\Authenticatable;

    protected $table = 'usr_login';

}
