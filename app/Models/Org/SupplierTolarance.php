<?php

namespace App\Models\Org;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class SupplierTolarance extends BaseValidator
{
    protected $table = 'org_supplier_tolarance';
    protected $primaryKey = 'id';
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';

    protected $fillable = ['supplier_id','category_id','subcategory_id','uom_id','qty','min','max','min_qty','max_qty'];

    protected $rules = array(
        'supplier_id' => 'required',

    );

    public function __construct()
    {
        parent::__construct();
        $this->attributes = array(
            'updated_by' => 2//Session::get("user_id")
          );
    }

   public function supplier()
		{
			 return $this->belongsTo('App\Models\Org\supplier' , 'supplier_id')->select(['supplier_id','supplier_name']);
		}
    public function itemCategory()
    {
       return $this->belongsTo('App\Models\Finance\Item\Category' , 'supplier_id')->select(['supplier_id','supplier_name']);
    }

}
