<?php

namespace App\Models\Merchandising;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\BaseValidator;

class StyleCreation extends BaseValidator {

    protected $table = 'style_creation';
    protected $primaryKey = 'style_id';

    const UPDATED_AT = 'updated_date';
    const CREATED_AT = 'created_date';

    //Validation functions......................................................

    /*
      *
      * unique:table,column,except,idColumn
      * The field under validation must not exist within the given database table
    */
    protected function getValidationRules($data) {
	return [
      'style_no' => [
        'required',
        'unique:style_creation,style_no,'.$data['style_id'].',style_id',
      ],
      'customer_id' => 'required',
      'division_id' => 'required',
      'product_feature_id' => 'required',
      'style_description' => 'required',
      'product_silhouette_id' => 'required',
  ];
}


    public function __construct() {
        parent::__construct();
        $this->attributes = array(
            'updated_by' => 2 //Session::get("user_id")
        );
    }

    public function GetStyleDetailsByCode($style_id) {

        return DB::table('style_creation')
                        ->join('prod_category', 'prod_category.prod_cat_id', '=', 'style_creation.product_category_id')
                        ->join('cust_customer', 'cust_customer.customer_id', '=', 'style_creation.customer_id')
                        ->leftjoin('product_feature', 'product_feature.product_feature_id', '=', 'style_creation.product_feature_id')
                        ->join('product_silhouette', 'product_silhouette.product_silhouette_id', '=', 'style_creation.product_silhouette_id')
                        ->join('cust_division', 'cust_division.division_id', '=', 'style_creation.division_id')
                        ->select('style_creation.style_description', 'prod_category.prod_cat_description', 'cust_customer.customer_name', 'product_feature.product_feature_description', 'product_silhouette.product_silhouette_description', 'cust_division.division_description', 'style_creation.image')
                        ->where('style_creation.style_id',$style_id)
                        ->get();



    }

    //default currency of the company
    public function currency() {
        return $this->belongsTo('App\Models\Finance\Customer', 'customer_id');
    }

    //Style Product Feature
    public function productFeature()
    {
        return $this->belongsToMany('App\Models\Merchandising\ProductFeature','style_product_feature','style_id','product_feature_id')
        ->withPivot('id');
    }

    public function customer() {
        return $this->belongsTo('App\Models\Org\Customer', 'customer_id');
    }

    public function division() {
        return $this->belongsTo('App\Models\Org\Division', 'division_id');
    }

    public function productType() {
        return $this->belongsTo('App\Models\Org\ProductType', 'pack_type_id');
    }

}
