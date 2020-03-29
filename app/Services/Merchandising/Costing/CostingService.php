<?php
namespace App\Services\Merchandising\Costing;

use Illuminate\Support\Facades\DB;

use App\Models\Merchandising\Costing\Costing;
use App\Models\Merchandising\Costing\CostingItem;
use App\Models\Merchandising\Costing\CostingSizeChart;
use App\Models\Merchandising\Costing\CostingFngColor;
use App\Models\Merchandising\Costing\CostingSfgColor;
use App\Models\Merchandising\Costing\CostingCountry;


use App\Models\Org\UOM;
use App\Models\Org\Color;
use App\Models\Merchandising\CustomerOrderDetails;
use App\Models\Merchandising\Item\Item;
use App\Models\Merchandising\Item\Category;
use App\Models\Merchandising\Item\SubCategory;
use App\Models\Merchandising\ProductComponent;
use App\Models\Merchandising\ProductSilhouette;
use App\Models\Merchandising\ProductFeature;
use App\Models\Org\Division;
use App\Models\Org\Season;
use App\Models\Merchandising\Costing\CostingFngItem;
use App\Models\Merchandising\Costing\CostingSfgItem;

use App\Models\Merchandising\BOMHeader;
use App\Models\Merchandising\BOMDetails;
use App\Services\Merchandising\Bom;
use App\Models\Org\SilhouetteClassification;
use App\Services\Merchandising\Bom\BomService;

class CostingService
{

  //generate finish godds and boms
  public function genarate_bom($costing_id){
    try {
        DB::beginTransaction();

        $costing = Costing::with(['style','buy'])->find($costing_id);
        $product_feature = ProductFeature::find($costing->style->product_feature_id);
        $fng_colors = CostingFngColor::where('costing_id', '=', $costing_id)->get();
        $countries = $this->get_costing_countries($costing_id);

        $category = Category::where('category_code', '=', 'FNG')->first();
        $sfg_category = Category::where('category_code', '=', 'SFG')->first();

        $product_silhouette = SilhouetteClassification::find($costing->style->product_silhouette_id);//ProductSilhouette::find($costing->style->product_silhouette_id);
        $division = Division::find($costing->style->division_id);
        //echo json_encode($division);die();
        $season = Season::find($costing->season_id);
        $uom = UOM::where('uom_code', '=', 'pcs')->first();

        //echo json_encode($costing_items);die();
        //finish good
        //style_code . silhoutte . division . color_code . season . country . buy name
        foreach ($fng_colors as $fng_color) {

          //$sfg_colors = CostingSfgColor::with(['product_component', 'product_silhouette'])->where('fng_color_id', '=', $fng_color->fng_color_id)->get();
          $sfg_colors = CostingSfgColor::where('fng_color_id', '=', $fng_color->fng_color_id)->get();

          foreach($countries as $country){
            //chek already has a fng
            $exists_fng_item = CostingFngItem::where('costing_id', '=', $costing_id)
              ->where('fng_color_id', '=', $fng_color->fng_color_id)
              ->where('country_id', '=', $country->country_id)->first();

            if($exists_fng_item == null) {//no finish good exists, create new one and generate bom
              //generate new fng
              $item = new Item();
              /*$description = $costing->style->style_no.'_'.$product_silhouette->product_silhouette_description.'_'
                .$division->division_code.'_'.$fng_color->color->color_code.'_'.$season->season_code.'_'.$country->country_code;*/
                //echo json_encode($fng_color);die();
              $description = $costing->style->style_no.'_'.$product_silhouette->sil_class_description.'_'
                  .$division->division_code.'_'.$fng_color->color->color_code.'_'.$season->season_code.'_'.$country->country_code;

              $description .= ($costing->buy == null) ? '' : ('_'.$costing->buy->buy_name);
            //  throw new \Exception("Some error message");

              $item_count = Item::where('master_description', '=', $description)->count();
              if($item_count > 0){
                continue;
              }

              $item->category_id = $category->category_id;
              $item->subcategory_id = 0;
              $item->master_description = $description;
              $item->parent_item_id = null;
              $item->inventory_uom = $uom->uom_id;
              $item->standard_price = null;
              $item->supplier_id = null;
              $item->supplier_reference = null;
              $item->color_wise = 1;
              $item->size_wise = null;
              $item->color_id = $fng_color->color_id;
              $item->status = 1;
              $item->created_by = $costing->created_by;
              $item->updated_by = $costing->updated_by;
              $item->user_loc_id = $costing->user_loc_id;
              $item->save();
              //generate item codes
              $item->master_code = $category->category_code . str_pad($item->master_id, 7, '0', STR_PAD_LEFT);
              $item->save();
              //throw new \Exception("Some error message");

              //echo json_encode($item);die();
              $fng_item = new CostingFngItem();
              $fng_item->costing_id = $costing_id;
              $fng_item->fng_id = $item->master_id;
              $fng_item->country_id = $country->country_id;
              $fng_item->fng_color_id = $fng_color->color_id;
              $fng_item->created_by = $costing->created_by;
              $fng_item->updated_by = $costing->updated_by;
              $fng_item->user_loc_id = $costing->user_loc_id;
              $fng_item->save();

              $bom_header = new BOMHeader();
              $bom_header->costing_id = $costing->id;
              $bom_header->fng_id = $item->master_id;
              $bom_header->country_id = $country->country_id;
              $bom_header->fob = $country->fob;
              $bom_header->total_smv = $costing->total_smv;
              $bom_header->epm = $costing->epm;
              $bom_header->np_margin = $costing->np_margine;
              $bom_header->finance_charges = $costing->finance_charges;
              $bom_header->finance_cost = $costing->finance_cost;
              $bom_header->fabric_cost = $costing->fabric_cost;
              $bom_header->elastic_cost = $costing->elastic_cost;
              $bom_header->trim_cost = $costing->trim_cost;
              $bom_header->packing_cost = $costing->packing_cost;
              $bom_header->other_cost = $costing->other_cost;
              $bom_header->total_rm_cost = $costing->total_rm_cost;
              $bom_header->labour_cost = $costing->labour_cost;
              $bom_header->coperate_cost = $costing->coperate_cost;
              $bom_header->total_cost = $costing->total_cost;
              $bom_header->revision_no = 0;
              $bom_header->status = 'RELEASED';
              $bom_header->created_by = $costing->created_by;
              $bom_header->updated_by = $costing->updated_by;
              $bom_header->user_loc_id = $costing->user_loc_id;
              $bom_header->save();

              if($product_feature->count > 1){//has semi finish goods
                //generate sfg items
                foreach ($sfg_colors as $sfg_color) {
                    $item2 = new Item();
                    $silhouette = ProductSilhouette::find($sfg_color->product_silhouette_id);
                    $component = ProductComponent::find($sfg_color->product_component_id);
                //echo json_encode($sfg_color->product_component_id);die();
                  /*  $description = $costing->style->style_no.'_'.$product_silhouette->product_silhouette_description.'_'
                      .$division->division_code.'_'.$fng_color->color->color_code.'_'.$season->season_code.'_'.$country->country_code;*/

                      $description = $costing->style->style_no.'_'.$product_silhouette->sil_class_description.'_'
                        .$division->division_code.'_'.$fng_color->color->color_code.'_'.$season->season_code.'_'.$country->country_code;

                    $description .= ($costing->buy == null) ? '' : ('_'.$costing->buy->buy_name);
                    $description .= '_'.$component->product_component_description.'_'.
                      $silhouette->product_silhouette_description.'_'.$sfg_color->color->color_code;

                    $description .= ($sfg_color->product_component_line_no == null) ? '' : ('_'.$sfg_color->product_component_line_no);

                    $sfg_item_count = Item::where('master_description', '=', $description)->count();
                    if($sfg_item_count > 0){
                      continue;
                    }

                    $item2->category_id = $sfg_category->category_id;
                    $item2->subcategory_id = 0;
                    $item2->master_description = $description;
                    $item2->parent_item_id = null;
                    $item2->inventory_uom = $uom->uom_id;
                    $item2->standard_price = null;
                    $item2->supplier_id = null;
                    $item2->supplier_reference = null;
                    $item2->color_wise = 1;
                    $item2->size_wise = null;
                    $item2->color_id = $sfg_color->color_id;
                    $item2->status = 1;
                    $item2->created_by = $costing->created_by;
                    $item2->updated_by = $costing->updated_by;
                    $item2->user_loc_id = $costing->user_loc_id;
                    $item2->save();
                    //generate item codes
                    $item2->master_code = $sfg_category->category_code . str_pad($item2->master_id, 7, '0', STR_PAD_LEFT);
                    $item2->save();

                    $sfg_item = new CostingSfgItem();
                    $sfg_item->costing_id = $costing_id;
                    $sfg_item->sfg_id = $item2->master_id;
                    $sfg_item->country_id = $country->country_id;
                    $sfg_item->sfg_color_id = $sfg_color->color_id;
                    $sfg_item->costing_fng_id = $fng_item->costing_fng_id;
                    $sfg_item->product_component_id = $sfg_color->product_component_id;
                    $sfg_item->product_silhouette_id = $sfg_color->product_silhouette_id;
                    $sfg_item->product_silhouette_id = $sfg_color->product_silhouette_id;
                    $sfg_item->product_component_line_no = $sfg_color->product_component_line_no;
                    $sfg_item->created_by = $costing->created_by;
                    $sfg_item->updated_by = $costing->updated_by;
                    $sfg_item->user_loc_id = $costing->user_loc_id;
                    $sfg_item->save();

                    $costing_items = CostingItem::where('costing_id', '=', $costing_id)
                    ->where('product_component_id', '=', $sfg_item->product_component_id)
                    ->where('product_silhouette_id', '=', $sfg_item->product_silhouette_id)
                    ->where('product_component_line_no', '=', $sfg_item->product_component_line_no)->get();
                    //create bom items
                    $this->create_bom_items($bom_header, $costing_items, $bom_header->created_by, $bom_header->user_loc_id, $sfg_item->sfg_id, $item2->master_code);
                }
              }
              else {//no sfg items
                $costing_items = CostingItem::where('costing_id', '=', $costing_id)->get();
                //create bom items
                $this->create_bom_items($bom_header, $costing_items, $bom_header->created_by, $bom_header->user_loc_id, null, null);
              }
            }
            else { //has genarated finish good

              $bom = BOMHeader::where('fng_id', '=', $exists_fng_item->fng_id)->first();
              $bom_service = new BomService();
              $bom_service->save_bom_revision($bom->bom_id);//save bom revision

              if($product_feature->count > 1){//has semi finish goods

                $sfg_items = CostingSfgItem::where('costing_fng_id', '=', $exists_fng_item->costing_fng_id)->get();
                foreach($sfg_items as $sfg_item) {
                  DB::insert("INSERT IGNORE INTO bom_details (bom_id, costing_item_id, costing_id, feature_component_id, product_component_id, product_silhouette_id,
                    inventory_part_id, position_id, purchase_uom_id, supplier_id, origin_type_id, garment_options_id, purchase_price, bom_unit_price,
                    net_consumption, wastage, gross_consumption, meterial_type, freight_charges, mcq, surcharge, total_cost, ship_mode, ship_term_id,
                    lead_time, country_id, comments, status)
                    SELECT
                    ? AS bom_id, costing_items.costing_item_id, costing_items.costing_id, costing_items.feature_component_id, costing_items.product_component_id,
                    costing_items.product_silhouette_id, costing_items.inventory_part_id, costing_items.position_id, costing_items.purchase_uom_id, null AS supplier_id,
                    costing_items.origin_type_id, costing_items.garment_options_id, costing_items.purchase_price, costing_items.unit_price AS bom_unit_price,
                    costing_items.net_consumption, costing_items.wastage, costing_items.gross_consumption, costing_items.meterial_type, costing_items.freight_charges,
                    costing_items.mcq, costing_items.surcharge, costing_items.total_cost, costing_items.ship_mode, costing_items.ship_term_id,
                    costing_items.lead_time, costing_items.country_id, costing_items.comments, 1 AS status
                    FROM costing_items
                    LEFT JOIN bom_details ON bom_details.costing_item_id = costing_items.costing_item_id
                    WHERE costing_items.costing_id = ?
                    AND costing_items.product_component_id = ?
                    AND costing_items.product_silhouette_id = ?
                    AND costing_items.product_component_line_no = ?
                    AND bom_details.bom_detail_id IS NULL", [$bom->bom_id, $costing_id, $sfg_item->product_component_id, $sfg_item->product_silhouette_id,
                      $sfg_item->product_component_line_no]);
                }
              }
              else {//no sfg items

                DB::insert("INSERT IGNORE INTO bom_details (bom_id, costing_item_id, costing_id, feature_component_id, product_component_id, product_silhouette_id,
                  inventory_part_id, position_id, purchase_uom_id, supplier_id, origin_type_id, garment_options_id, purchase_price, bom_unit_price,
                  net_consumption, wastage, gross_consumption, meterial_type, freight_charges, mcq, surcharge, total_cost, ship_mode, ship_term_id,
                  lead_time, country_id, comments, status)
                  SELECT
                  ? AS bom_id, costing_items.costing_item_id, costing_items.costing_id, costing_items.feature_component_id, costing_items.product_component_id,
                  costing_items.product_silhouette_id, costing_items.inventory_part_id, costing_items.position_id, costing_items.purchase_uom_id, null AS supplier_id,
                  costing_items.origin_type_id, costing_items.garment_options_id, costing_items.unit_price AS purchase_price, costing_items.unit_price AS bom_unit_price,
                  costing_items.net_consumption, costing_items.wastage, costing_items.gross_consumption, costing_items.meterial_type, costing_items.freight_charges,
                  costing_items.mcq, costing_items.surcharge, costing_items.total_cost, costing_items.ship_mode, costing_items.ship_term_id,
                  costing_items.lead_time, costing_items.country_id, costing_items.comments, 1 AS status
                  FROM costing_items
                  LEFT JOIN bom_details ON bom_details.costing_item_id = costing_items.costing_item_id
                  WHERE costing_items.costing_id = ?
                  AND bom_details.bom_detail_id IS NULL", [$bom->bom_id, $costing_id]);
              }

              $bomService = new BomService();
              $bomService->update_bom_summary($bom->bom_id);
            }
          }
        }
        DB::commit();// Commit Transaction

        return [
            'status' => 'success',
            'message' => 'Bom generated successfully.'
        ];
    }
    catch(\Exception $e){
      // Rollback Transaction
      echo json_encode($e);
      DB::rollback();
      return [
          'status' => 'error',
          'message' => $e->getMessage()
      ];
    }
  }

  //create bom items
  private function create_bom_items($bom, $costing_items, $user_id, $user_loc_id, $sfg_id, $sfg_code){
    foreach($costing_items as $costing_item) {
      $bom_detail = new BOMDetails();
      $bom_detail->bom_id = $bom->bom_id;
      $bom_detail->costing_item_id = $costing_item->costing_item_id;
      $bom_detail->costing_id = $bom->costing_id;
      $bom_detail->sfg_id = $sfg_id;
      $bom_detail->sfg_code = $sfg_code;
      $bom_detail->feature_component_id = $costing_item->feature_component_id;
      $bom_detail->product_component_id = $costing_item->product_component_id;
      $bom_detail->product_silhouette_id = $costing_item->product_silhouette_id;
      $bom_detail->product_component_line_no = $costing_item->product_component_line_no;
      $bom_detail->inventory_part_id = $costing_item->inventory_part_id;
      $bom_detail->position_id = $costing_item->position_id;
      $bom_detail->purchase_uom_id = $costing_item->purchase_uom_id;
      $bom_detail->supplier_id = $costing_item->supplier_id;
      $bom_detail->origin_type_id = $costing_item->origin_type_id;
      $bom_detail->garment_options_id = $costing_item->garment_options_id;
      $bom_detail->purchase_price = $costing_item->purchase_price;
      $bom_detail->bom_unit_price = $costing_item->unit_price;
      $bom_detail->net_consumption = $costing_item->net_consumption;
      $bom_detail->wastage = $costing_item->wastage;
      $bom_detail->gross_consumption = $costing_item->gross_consumption;
      $bom_detail->meterial_type = $costing_item->meterial_type;
      $bom_detail->freight_charges = $costing_item->freight_charges;
      $bom_detail->mcq = $costing_item->mcq;
      $bom_detail->surcharge = $costing_item->surcharge;
      $bom_detail->total_cost = $costing_item->total_cost;
      $bom_detail->ship_mode = $costing_item->ship_mode;
      $bom_detail->ship_term_id = $costing_item->ship_term_id;
      $bom_detail->lead_time = $costing_item->lead_time;
      $bom_detail->country_id = $costing_item->country_id;
      $bom_detail->comments = $costing_item->comments;
      $bom_detail->status = 1;
      $bom_detail->created_by = $user_id;
      $bom_detail->updated_by = $user_id;
      $bom_detail->user_loc_id = $user_loc_id;
      $bom_detail->save();
    }
  }


  private function get_costing_countries($costing_id){
    $list = DB::select("SELECT
      costing_country.*,
      org_country.country_description,
      org_country.country_code
      FROM costing_country
      INNER JOIN org_country ON org_country.country_id = costing_country.country_id
      WHERE costing_country.costing_id = ?", [$costing_id]);

      return $list;
  }

}
