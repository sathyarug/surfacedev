<?php

namespace App\Models\Merchandising\Costing;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use App\BaseValidator;
use App\Libraries\UniqueIdGenerator;

class BulkCostingDetails extends BaseValidator {

    protected $table = 'costing_bulk_details';
    protected $primaryKey = 'item_id';

    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';

    protected $fillable = ['item_id','bulk_costing_id'];


    public function getCostingItemDetails($costingId){

        /*return DB::table('costing_bulk_details')
                    ->join('item_master','item_master.master_id','costing_bulk_details.item_id')
                    ->join('item_subcategory', 'item_subcategory.subcategory_id','item_master.subcategory_id')
                    ->join('item_category','item_category.category_id','item_subcategory.category_id')
                    ->leftJoin('org_color','org_color.color_id','=','costing_bulk_details.color_id')
                    ->leftJoin('org_supplier','org_supplier.supplier_id','=','costing_bulk_details.supplier_id')
                    ->leftJoin('merc_color_options','merc_color_options.col_opt_id','=','costing_bulk_details.color_type_id')
                    ->leftJoin('merc_cut_direction','merc_cut_direction.cut_dir_id','=','costing_bulk_details.cut_dir_id')
                    ->join('org_uom','org_uom.uom_id','costing_bulk_details.uom_id')
                    ->join('org_size','org_size.size_id','costing_bulk_details.size_id')
                    ->select('item_master.master_id','item_master.master_description','costing_bulk_details.article_no','org_color.color_name','costing_bulk_details.net_consumption','costing_bulk_details.unit_price','costing_bulk_details.wastage','costing_bulk_details.gross_consumption','org_supplier.supplier_name','org_size.size_name','org_uom.uom_description','org_color.color_id','org_uom.uom_id','costing_bulk_details.moq','costing_bulk_details.mcq','costing_bulk_details.calculate_by_deliverywise','costing_bulk_details.order_type','costing_bulk_details.size_id','org_supplier.supplier_id','item_category.category_code','merc_color_options.color_option','merc_cut_direction.cut_dir_description')
                    ->where('costing_bulk_details.bulkheader_id',$costingId)
                    ->get();  */

         return DB::table('costing_bulk_details')
                    ->join('costing_bulk_feature_details','costing_bulk_feature_details.blk_feature_id','costing_bulk_details.bulkheader_id')
                    ->join('item_master','item_master.master_id','costing_bulk_details.main_item')
                    ->join('item_subcategory', 'item_subcategory.subcategory_id','item_master.subcategory_id')
                    ->join('item_category','item_category.category_id','item_subcategory.category_id')
                    ->leftJoin('org_color','org_color.color_id','=','costing_bulk_details.color_id')
                    ->leftJoin('org_supplier','org_supplier.supplier_id','=','costing_bulk_details.supplier_id')
                    ->leftJoin('merc_color_options','merc_color_options.col_opt_id','=','costing_bulk_details.color_type_id')
                    ->join('org_uom','org_uom.uom_id','costing_bulk_details.uom_id')
                    ->leftJoin('org_size','org_size.size_id','costing_bulk_details.size_id')
                    ->join('product_component','product_component.product_component_id','costing_bulk_feature_details.component_id')
                    ->select('item_master.master_id','item_master.master_description','costing_bulk_details.article_no','org_color.color_name','costing_bulk_details.net_consumption','costing_bulk_details.unit_price','costing_bulk_details.wastage','costing_bulk_details.gross_consumption','org_supplier.supplier_name','org_size.size_name','org_uom.uom_description','org_color.color_id','org_uom.uom_id','costing_bulk_details.moq','costing_bulk_details.mcq','costing_bulk_details.calculate_by_deliverywise','costing_bulk_details.order_type','costing_bulk_details.size_id','org_supplier.supplier_id','item_category.category_code','merc_color_options.color_option','product_component.product_component_description','product_component.product_component_id')
                    ->where('costing_bulk_feature_details.bulkheader_id',$costingId)
                    ->where('costing_bulk_details.status','1')
                    ->get();

    }

}
