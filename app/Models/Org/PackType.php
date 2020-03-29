<?php

namespace App\Models\Org;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class PackType extends BaseValidator
{
    protected $table='style_type';
    protected $primaryKey='style_type';
    public $incrementing = false;

    public function __construct() {
        parent::__construct();
    }
}
