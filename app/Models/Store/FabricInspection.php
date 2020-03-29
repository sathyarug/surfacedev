<?php


namespace App\Models\Store;

use Illuminate\Database\Eloquent\Model;

use App\BaseValidator;



class FabricInspection extends BaseValidator
{
    protected $table='store_fabric_inspection';
    protected $primaryKey='fab_inspection_id';
    //public $timestamps = false;
    const UPDATED_AT='updated_date';
    const CREATED_AT='created_date';

    protected $fillable=['roll_plan_id','roll_plan_id', 'lot_no','invoice_no', 'batch_no', 'roll_no', 'qty', 'received_qty', 'bin', 'width','shade','inspection_status','comment','lab_comment'
    ];

    protected $rules=array(
        ////'color_code'=>'required',
        //'color_name'=>'required'
    );

    public function __construct() {
        parent::__construct();
    }




}
