<?php
/**
 * Created by PhpStorm.
 * User: sankap
 * Date: 5/1/2019
 * Time: 11:41 AM
 */

namespace App\Models\Store;
use App\BaseValidator;


class IssueDetails extends BaseValidator
{
    protected $table='store_issue_detail';
    protected $primaryKey='issue_detail_id';
    const UPDATED_AT='updated_date';
    const CREATED_AT='created_date';

    protected $fillable=['issue_id','so_no', 'cus_po','item_code','material_code', 'color','size', 'grn_no', 'qty', 'uom' ];

    protected $rules=array(
        ////'color_code'=>'required',
        //'color_name'=>'required'
    );

    public function __construct() {
        parent::__construct();
    }

    public function issue(){
        return $this->belongsTo('App\Models\Store\IssueHeader');
    }

    public static function getIssueDetailsForReturn($id){
        $result = self::join('merc_customer_order_header AS c', 'c.order_id', '=', 'store_issue_detail.so_no')
            ->join('store_issue_header AS h', 'h.issue_id', '=', 'store_issue_detail.issue_id')
            ->join('merc_customer_order_details AS d', 'd.details_id', '=', 'store_issue_detail.cus_po')
            ->join('item_master AS m', 'm.master_id', '=', 'store_issue_detail.material_code')
            ->join('org_color AS l', 'l.color_id', '=', 'store_issue_detail.color')
            ->join('org_size AS s', 's.size_id', '=', 'store_issue_detail.size')
            ->join('org_uom AS u', 'u.uom_id', '=', 'store_issue_detail.uom')
            ->where('store_issue_detail.issue_id', '=', $id)
            ->select('c.order_code', 'd.po_no', 'm.master_id', 'm.master_description', 'l.color_name', 's.size_name', 'store_issue_detail.qty', 'h.issue_no', 'store_issue_detail.id', 'store_issue_detail.id', 'u.uom_description')
            ->get();
        return $result;
    }


}
