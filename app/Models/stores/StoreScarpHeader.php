<?php

namespace App\Models\stores;
use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class StoreScarpHeader extends BaseValidator
{
    protected $table='store_inv_scarp_header';
    protected $primaryKey='report_no';
    const UPDATED_AT='updated_date';
    const CREATED_AT='created_date';

    protected $fillable=['from_store','to_store','status'];

    protected $rules=array();

    public function __construct() {
        parent::__construct();
    }


}
