<?php

namespace App\Models\Merchandising;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class PoOrderApproval extends BaseValidator
{
    protected $table = 'merc_po_order_approval';
    protected $primaryKey = 'email_id';

    const UPDATED_AT = 'updated_date';
    const CREATED_AT = 'created_date';


    /*protected $rules = array(

    );*/

    public function __construct() {
        parent::__construct();
    }




}
