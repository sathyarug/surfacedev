<?php

namespace App\Models\Merchandising;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class Round extends BaseValidator
{
    protected $table='merc_round';
    protected $primaryKey='round_id';
    const UPDATED_AT='updated_date';
    const CREATED_AT='created_date';

    protected $fillable=['round_id'];

    protected $rules=array(
        'round_id'=>'required'
    );

    public function __construct() {
        parent::__construct();
    }
}
