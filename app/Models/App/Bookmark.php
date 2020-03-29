<?php

namespace App\Models\App;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class Bookmark extends BaseValidator
{
    protected $table='app_bookmark';
    protected $primaryKey='id';
    public $timestamps = false;

    protected $fillable = ['url','name'];

    protected $rules = [
      'url' => 'required',
      'name' => 'required'
    ];

    public function __construct()
    {
        parent::__construct();
    }
}
