<?php

namespace App\Models\IE;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class SMVUpdateHistory extends BaseValidator
{
    protected $table='ie_smv_his';
    protected $primaryKey='smv_his_id';
    const UPDATED_AT='updated_date';
    const CREATED_AT='created_date';

    protected $fillable=['smv_his_id','customer_id','division_id','product_silhouette_id','version','min_smv','max_smv'];

    protected $rules=array(
        //'customer_id'=>'required',
        //'division_id'=>'required',
        'product_silhouette_id'=>'required'
    );

    public function __construct() {
        parent::__construct();
    }

    public function customer()
		{
			// return $this->belongsTo('App\Models\Org\Customer' , 'customer_id')->select(['customer_id','customer_name']);
		}

    public function silhouette()
		{
			 return $this->belongsTo('App\Models\Org\Silhouette' , 'product_silhouette_id')->select(['product_silhouette_id','product_silhouette_description']);
		}
}
