<?php

namespace App\Models\Merchandising\Item;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use App\BaseValidator;

class Item_History extends BaseValidator
{
  public function __construct()
  {
    //add functions names to 'except' paramert to skip authentication
    parent::__construct();
  }

    protected $table = 'item_master_history';
    protected $primaryKey = 'master_his_id';
    const UPDATED_AT = 'updated_date';
    const CREATED_AT = 'created_date';



}
