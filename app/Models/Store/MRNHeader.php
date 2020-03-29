<?php
namespace App\Models\Store;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;


class MRNHeader extends  BaseValidator
{
    protected $table='store_mrn_header';
    protected $primaryKey='mrn_id';
    const UPDATED_AT='updated_date';
    const CREATED_AT='created_date';

    protected $fillable=['mrn_no','location', 'section'];

    protected $rules=array(
        ////'color_code'=>'required',
        //'color_name'=>'required'
    );

    public function __construct() {
        parent::__construct();
    }

    public function grnDetails(){
        return $this->hasMany('App\Models\Store\MRNDetail', 'mrn_id', 'mrn_id');
    }

    public static function getMRNList($soNo){
        $mrnData = MRNHeader::where('d.so_no', '=', $soNo)
            ->select('store_mrn_header.mrn_id', 'store_mrn_header.mrn_no')
            ->join("store_mrn_detail AS d", "d.mrn_id", "=", "store_mrn_header.mrn_id")
            ->get();

        return $mrnData;
    }

    public static function getMrnData($mrn){
        $mrnData = MRNHeader::where('store_mrn_header.mrn_id', '=', $mrn)
            ->select('store_mrn_header.mrn_id', 'store_mrn_header.mrn_no')
            ->join("store_mrn_detail AS d", "d.mrn_id", "=", "store_mrn_header.mrn_id")
            ->get();

        return $mrnData;
    }
}
