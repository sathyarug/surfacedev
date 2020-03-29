<?php

namespace App\Models\Org;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class Size extends BaseValidator
{

    protected $table = 'org_size';
    protected $primaryKey = 'size_id';
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';


    protected $fillable = ['size_id','size_name', 'status'];
    // protected $rules=array(
    //     'size_name'=>'required'
    // );

    public function __construct() {
        parent::__construct();
    }

    //Validation functions......................................................

    /**
    *unique:table,column,except,idColumn
    *The field under validation must not exist within the given database table
    */
    protected function getValidationRules($data /*model data with attributes*/) {
      return [
          'size_name' => [
            'required',
            'unique:org_size,size_name,'.$data['size_id'].',size_id',
          ],
          //'category_id' => 'required',
          //'subcategory_id' => 'required'
      ];
    }

    //relationships.............................................................

    public function category()
		{
			 return $this->belongsTo('App\Models\Finance\Item\Category' , 'category_id')->select(['category_id','category_name']);
		}

    public function subCategory()
		{
			 return $this->belongsTo('App\Models\Finance\Item\SubCategory' , 'subcategory_id')->select(['subcategory_id','subcategory_name']);
		}

  /*  public function scopeLoadCustomeSizeList(){

       return '';//DB::table('cust_sizes')->join('cust_division','cust_division.division_id','=','cust_sizes.division_id' )->join('cust_customer','cust_customer.customer_id','=','cust_sizes.customer_id')->select('cust_sizes.*','cust_division.division_description','cust_customer.customer_name')->where('cust_sizes.status','=','1')->get();

    }*/



}
