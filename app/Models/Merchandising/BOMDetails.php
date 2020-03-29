<?php

namespace App\Models\Merchandising;

use App\BaseValidator;
use Illuminate\Support\Facades\DB;

class BOMDetails extends BaseValidator
{

    protected $table = 'bom_details';
    protected $primaryKey = 'bom_detail_id';
    public $timestamps = false;

    protected $fillable = [
      'feature_component_id', 'inventory_part_id', 'costing_id', 'position_id', 'purchase_uom_id', 'origin_type_id', 'garment_options_id',
    'bom_unit_price', 'net_consumption', 'wastage', 'gross_consumption', /*'meterial_type',*/ 'freight_charges', 'mcq',
    'surcharge', 'total_cost', 'ship_mode', 'ship_term_id', 'lead_time', 'country_id', 'comments','bom_id',
    'product_component_id', 'product_silhouette_id', 'product_component_line_no', 'supplier_id', 'sfg_code', 'sfg_id', 'purchase_price'];

    //Validation functions......................................................
    /**
    *unique:table,column,except,idColumn
    *The field under validation must not exist within the given database table
    */
    protected function getValidationRules($data /*model data with attributes*/) {
      return [
          /*'color_code' => [
            'required',
            'unique:org_color,color_code,'.$data['color_id'].',color_id',
          ],
          'color_name' => 'required'*/
      ];
    }






    public function GetBOMDetails($bomId){

        /*return  DB::table('bom_details')
                  ->join('item_master','item_master.master_id','bom_details.master_id')
                  ->join('item_subcategory', 'item_subcategory.subcategory_id','item_master.subcategory_id')
                  ->join('item_category','item_category.category_id','item_subcategory.category_id')
                  ->join('org_color','org_color.color_id','bom_details.item_color')
                  ->join('org_size','org_size.size_id','bom_details.item_size')
                  ->join('org_uom','org_uom.uom_id','bom_details.uom_id')
                  ->join('bom_header','bom_header.bom_id','bom_details.bom_id')
                  ->leftJoin('org_supplier','org_supplier.supplier_id','=','bom_details.supplier_id')
                  ->join('costing_bulk_details',function($join){
                    $join->on('costing_bulk_details.bulkheader_id','=','bom_header.costing_id')
                         ->on('costing_bulk_details.item_id','=','bom_details.master_id');
                    })
                  ->leftJoin('merc_color_options','merc_color_options.col_opt_id','=','costing_bulk_details.color_type_id')
                  ->leftJoin('merc_cut_direction','merc_cut_direction.cut_dir_id','=','costing_bulk_details.cut_dir_id')
                  ->select('item_master.master_id','item_master.master_description','bom_details.artical_no','org_color.color_name','org_size.size_name','org_uom.uom_description','org_uom.uom_id','bom_details.conpc','bom_details.item_wastage','bom_details.unit_price','bom_details.total_qty','bom_details.total_value','org_color.color_id','costing_bulk_details.moq','costing_bulk_details.mcq','org_size.size_id','org_supplier.supplier_name','org_supplier.supplier_id','merc_color_options.color_option','merc_cut_direction.cut_dir_description','item_category.category_code')
                  ->where('bom_details.bom_id',$bomId)
                  ->get();*/

        return  DB::table('bom_details')
                ->join('item_master','item_master.master_id','bom_details.master_id')
                ->join('item_subcategory', 'item_subcategory.subcategory_id','item_master.subcategory_id')
                ->join('item_category','item_category.category_id','item_subcategory.category_id')
                ->leftJoin('org_color','org_color.color_id','bom_details.item_color')
                ->leftJoin('org_size','org_size.size_id','bom_details.item_size')
                ->join('org_uom','org_uom.uom_id','bom_details.uom_id')
                ->join('bom_header','bom_header.bom_id','bom_details.bom_id')
                ->leftJoin('org_supplier','org_supplier.supplier_id','=','bom_details.supplier_id')
                ->join('costing_bulk_feature_details',function($joins){
                    $joins->on('costing_bulk_feature_details.bulkheader_id','=','bom_header.costing_id')
                         ->on('costing_bulk_feature_details.component_id','=','bom_details.component_id');
                })
                ->join('product_component','product_component.product_component_id','bom_details.component_id')
                ->join('costing_bulk_details',function($join){
                    $join->on('costing_bulk_details.bulkheader_id','=','costing_bulk_feature_details.blk_feature_id')
                         ->on('costing_bulk_details.main_item','=','bom_details.master_id');
                 })
                ->leftJoin('merc_color_options','merc_color_options.col_opt_id','=','costing_bulk_details.color_type_id')
                ->select('item_master.master_id','item_master.master_description','bom_details.artical_no','org_color.color_name','org_size.size_name',
                'org_uom.uom_description','org_uom.uom_id','bom_details.conpc','bom_details.item_wastage','bom_details.unit_price',
                'bom_details.total_qty','bom_details.total_value','org_color.color_id','costing_bulk_details.moq','costing_bulk_details.mcq',
                'org_size.size_id','org_supplier.supplier_name','org_supplier.supplier_id','merc_color_options.color_option','item_category.category_code',
                'product_component.product_component_description','product_component.product_component_id')
                ->where('bom_header.bom_id',$bomId)
                ->where('costing_bulk_details.status',1)
                ->get();




    }
}
