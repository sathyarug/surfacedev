<?php

namespace App\Models\Merchandising;

use Illuminate\Database\Eloquent\Model;

use App\BaseValidator;
use App\Libraries\UniqueIdGenerator;

class PurchaseOrderGeneral extends BaseValidator
{
    protected $table='merc_po_order_general_header';
    protected $primaryKey='po_id';
    const UPDATED_AT='updated_date';
    const CREATED_AT='created_date';

    protected $fillable=['po_type','po_sup_code','po_deli_loc','po_def_cur','po_status'];

    protected $rules=array(
        'po_type'=>'required',
        'po_sup_code' => 'required',
        'po_deli_loc' => 'required',
        'po_def_cur' => 'required'
    );

    public function __construct() {
        parent::__construct();
    }
	
	//default currency of the company
	public function currency()
	{
		return $this->belongsTo('App\Models\Finance\Currency' , 'po_def_cur');
	}
	
	public function location()
		{
			return $this->belongsTo('App\Models\Org\Location\Location' , 'po_deli_loc');
		}
		
		
	public function supplier()
		{
			return $this->belongsTo('App\Models\Org\Supplier' , 'po_sup_code');
		}


    public static function boot()
    {
        static::creating(function ($model) {
          $user = auth()->user();
          $code = UniqueIdGenerator::generateUniqueId('PO_GENERAL' , $user->location);
          $model->po_number = 'G'.$code;
          //$model->updated_by = $user->user_id;
        });

        /*static::updating(function ($model) {
            $user = auth()->user();
            $model->updated_by = $user->user_id;
        });*/

        parent::boot();
    }


}
