<?php

namespace App\Models\Merchandising;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class MasAction extends BaseValidator
{
    protected $table='mas_action';
    protected $primaryKey='action_id';
    const UPDATED_AT='updated_date';
    const CREATED_AT='created_date';

    protected $fillable=['action_name','action_description','action_id'];

    protected $rules=array(
        'action_name'=>'required'
    );

    public function __construct() {
        parent::__construct();
    }
}
