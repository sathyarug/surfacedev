<?php

namespace App\Models\IE;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class SMVUpdate extends BaseValidator
{
    protected $table='smv_update';
    protected $primaryKey='smv_id';
    const UPDATED_AT='updated_date';
    const CREATED_AT='created_date';

    protected $fillable=['smv_id','customer_id','division_id','product_silhouette_id','version','min_smv','max_smv'];

    // protected $rules=array(
    //     'customer_id'=>'required',
    //     'division_id'=>'required',
    //     'product_silhouette_id'=>'required'
    // );

    //Validation Functions
    /**
    *unique:table,column,except,idColumn
    *The field under validation must not exist within the given database table
    **/
    protected function getValidationRules($data) {
      return [
          //'customer_id' => 'required',
          //'division_id' => 'required',
          'product_silhouette_id' => 'required'
      ];
    }

    protected $casts = [
    'min_smv' => 'double',
    'max_smv'=>'double'
    ];

    public function __construct() {
        parent::__construct();
    }

    public function customer()
		{
			 //return $this->belongsTo('App\Models\Org\Customer' , 'customer_id')->select(['customer_id','customer_name']);
		}

    public function silhouette()
		{
			 return $this->belongsTo('App\Models\Org\Silhouette' , 'product_silhouette_id')->select(['product_silhouette_id','product_silhouette_description']);
		}
    public function division()
    {
       //return $this->belongsTo('App\Models\Org\Division' , 'division_id')->select(['division_id','division_description']);
    }

}
