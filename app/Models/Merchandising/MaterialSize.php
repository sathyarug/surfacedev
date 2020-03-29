<?php

namespace App\Models\Merchandising;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class MaterialSize extends BaseValidator
{
    protected $table='org_size';
    protected $primaryKey='size_id';
    const UPDATED_AT='updated_date';
    const CREATED_AT='created_date';

    protected $fillable=['category_id','subcategory_id','size_id','size_name','po_status', 'division_id'];

    protected $rules=array(
        'category_id'=>'required',
        'subcategory_id'=>'required',
        'size_name'=>'required'
    );

    public function __construct() {
        parent::__construct();
    }

    public function category()
		{
			 return $this->belongsTo('App\Models\Finance\Item\Category' , 'category_id')->select(['category_id','category_name']);
		}

    public function subCategory()
		{
			 return $this->belongsTo('App\Models\Finance\Item\SubCategory' , 'subcategory_id')->select(['subcategory_id','subcategory_name']);
		}
}
