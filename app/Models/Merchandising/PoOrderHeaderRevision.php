<?php

namespace App\Models\Merchandising;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\BaseValidator;
use App\Libraries\UniqueIdGenerator;

class PoOrderHeaderRevision extends BaseValidator
{
    protected $table='merc_po_order_header_revision';
    protected $primaryKey='po_id';
    const UPDATED_AT='updated_date';
    const CREATED_AT='created_date';

    protected $fillable=['po_type','po_sup_code','po_deli_loc','po_def_cur','po_status','order_type','delivery_date','invoice_to','pay_mode','pay_term','ship_mode','po_date','prl_id','ship_term','special_ins'];

    protected $dates = ['delivery_date','po_date'];
    protected $rules=array(
        'po_type'=>'required',
        'po_sup_code' => 'required',
        'po_deli_loc' => 'required',
        'po_def_cur' => 'required',
        'pay_mode' => 'required',
        'pay_term' => 'required',
        'ship_mode' => 'required',
        'po_date' => 'required',
        'prl_id' => 'required',
        'ship_term' => 'required'
    );


    public function __construct() {
        parent::__construct();
    }

    public function setDiliveryDateAttribute($value)
		{
    	$this->attributes['delivery_date'] = date('Y-m-d', strtotime($value));
    }

    /*public function getDiliveryDateAttribute($value){
    $this->attributes['delivery_date'] = date('d F,Y', strtotime($value));
    return $this->attributes['delivery_date'];
    }*/

    public function setpoDateAttribute($value)
		{
    	$this->attributes['po_date'] = date('Y-m-d', strtotime($value));
    }

    /*public function getpoDateAttribute($value){
    $this->attributes['po_date'] = date('d F,Y', strtotime($value));
    return $this->attributes['delivery_date'];
    }*/

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

          //     if ($model->po_type == 'BULK'){$rep = 'BUL';}
          // elseif ($model->po_type == 'GENERAL'){$rep = 'GEN';}
          // elseif ($model->po_type == 'GREAIGE'){$rep = 'GRE';}
          // elseif ($model->po_type == 'RE-ORDER'){$rep = 'REO';}
          // elseif ($model->po_type == 'SAMPLE'){$rep = 'SAM';}
          // elseif ($model->po_type == 'SERVICE'){$rep = 'SER';}
          // $user = auth()->payload();
          // $user_loc = $user['loc_id'];
          // $code = UniqueIdGenerator::generateUniqueId('PO_MANUAL' , $user_loc);
          // $model->po_number = $rep.$code;
          // $model->loc_id = $user_loc;


        });

        /*static::updating(function ($model) {
            $user = auth()->pay_loa();
            $model->updated_by = $user->user_id;
        });*/

        parent::boot();
    }

    public function poDetails(){
        return $this->belongsTo('App\Models\Merchandising\PoOrderDetails' , 'po_id');
    }
}
