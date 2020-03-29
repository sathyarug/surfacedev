<?php

namespace App\Models\Store;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class StoreBinAllocation extends BaseValidator {

    protected $table = 'org_store_bin_allocation';
    protected $primaryKey = 'allocation_id';

    const UPDATED_AT = 'updated_date';
    const CREATED_AT = 'created_date';

    protected $fillable = ['store_bin_id', 'max_capacity', 'width', 'height', 'item_category_id', 'item_subcategory_id', 'length'];
    protected $rules = array(
            /* 'width'=>'required',
              'height'=>'required',
              'length'=>'required' */
    );

    public function __construct() {

        parent::__construct();
    }

    static function getAllocatedItemByBin($binId) {
        return StoreBinAllocation::select('*')
                        ->where([['store_bin_id', '=', $binId], ['status', '=', 1]])->get();
    }

    static function getExistRecords($store_bin_id, $item_category_id, $item_subcategory_id) {
        return StoreBinAllocation::select('*')
                        ->where([
                            ['store_bin_id', '=', $store_bin_id],
                            ['status', '=', 1],
                            ['item_category_id', '=', $item_category_id],
                            ['item_subcategory_id', '=', $item_subcategory_id]
                                ]
                        )->get();
    }

}
