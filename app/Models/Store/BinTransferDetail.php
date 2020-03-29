<?php

namespace App\Models\Store;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class BinTransferDetail extends Model
{
    protected $table='store_bin_transfer_detail';
    protected $primaryKey='id';
    public $timestamps = false;
    //const UPDATED_AT='updated_date';
    //const CREATED_AT='created_date';

    protected $fillable=['id','transfer_id','sub_store','from_bin','to_bin', 'qty', 'customer_po_id'];

    protected $rules=array(
        'sub_store'=>'required',
        'from_bin'=>'required',
        'to_bin'=>'required'
    );

    public function __construct() {
        
        parent::__construct();
    }
    

}
