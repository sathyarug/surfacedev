<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\BaseValidator;

class itemCreation extends BaseValidator
{

  var $authorize = null;

  public function __construct()
  {
    //add functions names to 'except' paramert to skip authentication
    parent::__construct();
  }

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'item_master';




    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'master_id';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['master_id', 'subcategory_id', 'master_code', 'master_description', 'uom_id', 'status'];

    public function LoadItems(){

        return DB::table('item_master')
                 ->join('item_subcategory','item_subcategory.subcategory_id','=','item_master.subcategory_id')
                 ->join('item_category','item_category.category_id','=','item_subcategory.category_id')
                 ->select('item_master.master_id','item_category.category_name','item_master.master_description','item_master.status')->get();

    }

}
