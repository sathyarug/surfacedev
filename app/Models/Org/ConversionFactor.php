<?php

namespace App\Models\Org;
use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class ConversionFactor extends BaseValidator
{

    protected $table = 'conversion_factor';
    protected $primaryKey = 'conv_id';

    public function __construct()
    {
        parent::__construct();
    }


}
