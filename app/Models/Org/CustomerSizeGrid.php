<?php

namespace App\Models\Org;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class CustomerSizeGrid extends BaseValidator{

  protected $table='cust_size_grid';
  protected $primaryKey='id';
  const CREATED_AT = 'created_date';
  const UPDATED_AT = 'updated_date';


  protected $fillable = ['id','customer_id','product_silhouette_id','size_id'];

  protected $rules = array(
      'customer_id' => 'required',
      'product_silhouette_id' => 'required',
      'size_id' => 'required'


  );


  public function __construct()
  {
      parent::__construct();
      $this->attributes = array(
          'updated_by' => 2//Session::get("user_id")
        );
  }
  public function customer(){
    return $this->belongsTo('App\Models\Org\Customer','customer_id');


  }
  public function productSilhouette(){
    return $this->belongsTo('App\Models\Org\Silhouette','product_silhouette_id');


  }
  public function size(){
    return $this->belongsTo('App\Models\Org\Size','size_id');


  }


}
