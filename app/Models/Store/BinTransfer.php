<?php

namespace App\Models\Store;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class BinTransfer extends Model
{
    protected $table='store_bin_transfer_header';
    protected $primaryKey='id';
    //public $timestamps = false;
    const UPDATED_AT='updated_date';
    const CREATED_AT='created_date';

    protected $fillable=['transfer_id', 'sub_store','created_by','updated_by'];

    protected $rules=array(
        'sub_store'=>'required',
        'from_bin'=>'required',
        'to_bin'=>'required'
    );

    public function __construct() {

        parent::__construct();
    }

    public function binTransferDetails(){
        return $this->hasMany('App\Models\Store\BinTransferDetail', 'transfer_id');
    }

    public static function getAddedBinStock($id){

        $data = self::where('store_bin_transfer_header.transfer_id', $id)
            ->join("store_bin_transfer_detail AS d", "store_bin_transfer_header.transfer_id", "=", "d.transfer_id")
            ->join("item_master AS i", "i.master_id", "=", "d.material_id")
            ->join("org_color AS c", "c.color_id", "=", "d.color")
            ->join("org_size AS s", "s.size_id", "=", "d.size")
            ->join("org_uom AS u", "u.uom_id", "=", "d.uom")
            ->join("style_creation AS y", "y.style_id", "=", "d.style")
            ->select( "c.color_name","s.size_name", "u.uom_code", "i.master_description", "i.master_id", "d.qty", "y.style_no", "c.color_id", "s.size_id", "i.master_id", "u.uom_id", "y.style_id")
            ->get()
            ->toArray();

        return $data;
    }

    public static function getBinTransferDeiatls($transferId){
        $data = self::where('store_bin_transfer_header.transfer_id', $transferId)
            ->join("store_bin_transfer_detail AS d", "store_bin_transfer_header.transfer_id", "=", "d.transfer_id")
            ->select( "d.to_bin","d.from_bin", "d.style", "d.color", "d.size", "d.material_id", "d.qty", "d.uom", "d.transfer_id", "d.sub_store", "d.sub_store","d.from_bin","d.customer_po_id")
            ->get()
            ->toArray();

        return $data;
    }
}