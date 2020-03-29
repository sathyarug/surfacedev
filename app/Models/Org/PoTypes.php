<?php

namespace App\Models\Org;
use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class PoTypes extends BaseValidator
{
    protected $table = 'org_po_types';
    protected $primaryKey = 'po_id';
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';

    protected $fillable = ['po_type','process_type'];

    protected $rules = array(
        'po_type' => 'required',
        'process_type' => 'required'
    );

    public function __construct()
    {
        parent::__construct();
    }


}
