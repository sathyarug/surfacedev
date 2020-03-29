<?php

namespace App\Http\Controllers\Merchandising\Costing;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use App\Libraries\AppAuthorize;

use App\Models\Merchandising\Costing\Costing;
//use App\Models\Merchandising\Costing\CostingFinishGood;
//use App\Models\Merchandising\Costing\CostingFinishGoodComponent;
//use App\Models\Merchandising\Costing\CostingFinishGoodComponentItem;
use App\Models\Merchandising\Costing\CostingItem;
use App\Models\Org\Country;
use App\Models\Merchandising\Item\Category;
use App\Models\Merchandising\Item\Item;
use App\Models\Org\UOM;
use App\Models\Org\Color;
use App\Models\Org\Supplier;
use App\Models\Org\GarmentOptions;
use App\Models\Finance\ShipmentTerm;
use App\Models\Merchandising\Position;
use App\Models\Org\OriginType;
use App\Models\Merchandising\ProductFeatureComponent;


class CostingItemController extends Controller
{
    var $authorize = null;

    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => ['index']]);
      $this->authorize = new AppAuthorize();
    }

    //Display a listing of the resource.
    public function index(Request $request)
    {
       $type = $request->type;

       if($type == 'costing_items'){
         $costing_id = $request->costing_id;
         $feature_component_id = $request->feature_component_id;
         return response([
           'data' => $this->get_items($costing_id, $feature_component_id)
        ]);
       }
       else if($type == 'costing_finish_good_items'){
         $costing_id = $request->costing_id;
         return response([
           'data' => $this->get_finish_good_items($costing_id)
        ]);
       }
       /*else if($type == 'aa')    {
         $search = $request->search;
         return response($this->get_item(10));
       }*/
      /* else{
         return response([]);
       }*/
    }


    //create new country
    public function store(Request $request)
    {
        $item_data = $this->generate_item_data($request->item_data);
        //  echo json_encode($item_data);die();
        $costing_item = new CostingItem();
        if($costing_item->validate($item_data))
        {
          $costing_item->fill($item_data);
          $costing_item->status = 1;
          $costing_item->save();

          $this->update_costing_summary_after_modify_item($costing_item->costing_id);

          $saved_item = $this->get_item($costing_item->costing_item_id);
          $saved_item['edited'] = false;

          $costing = Costing::find($costing_item->costing_id);

          return response([
            'data' => [
              'message' => 'Costing item saved successfully',
              'item' => $saved_item,
              'costing' => $costing
            ]
          ] , Response::HTTP_CREATED );
        }
        else{
          $errors = $costing_item->errors();// failure, get errors
          return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }


    //get new country
    public function show($id)
    {
    /*  if($this->authorize->hasPermission('COUNTRY_MANAGE'))//check permission
      {
        $country = Country::find($id);
        if($country == null)
          return response( ['data' => 'Requested country not found'] , Response::HTTP_NOT_FOUND );
        else
          return response( ['data' => $country] );
      }
      else{
        return response($this->authorize->error_response(), 401);
      }*/
    }


    //update country
    public function update(Request $request, $id)
    {
      $item_data = $this->generate_item_data($request->item_data);
      //  echo json_encode($item_data);die();
      $item = CostingItem::find($id);
      if($item->validate($item_data))
      {
      //  echo json_encode($item_data);die();
        $item->fill($item_data);
        $item->save();

        $this->update_costing_summary_after_modify_item($item->costing_id);

        $saved_item = $this->get_item($item->costing_item_id);
        $saved_item['edited'] = false;

        $costing = Costing::find($item->costing_id);
        return response([
          'data' => [
            'message' => 'Item saved successfully',
            'item' => $saved_item,
            'costing' => $costing
          ]
        ]);
      }
      else{
        $errors = $item->errors();// failure, get errors
        return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
      }
    }


    public function save_items(Request $request){
      $items = $request->items;
      $costing_id = 0;
      if(sizeof($items)){
        for($x = 0 ; $x < sizeof($items) ; $x++){
          $item = null;
          if($items[$x]['costing_item_id'] <= 0){
            $item = new CostingItem();
            $item->status = 1;
          }
          else{
            $item = CostingItem::find($items[$x]['costing_item_id']);
          }

          $item_data = $this->generate_item_data($items[$x]);
          $item->fill($item_data);
          $item->save();
        }

        $costing_id = $items[0]['costing_id'];
        $this->update_costing_summary_after_modify_item($costing_id);
      }

      $costing = Costing::find($costing_id);

      return response([
        'data' => [
          'message' => 'Items saved successfully',
          'items' => $this->get_items($costing_id, $items[0]['feature_component_id']),
          'costing' => $costing
        ]
      ]);
    }

    //deactivate a country
    public function destroy($id)
    {
        $item = CostingItem::find($id);
        $item->delete();

        $this->update_costing_summary_after_modify_item($item->costing_id);
        $costing = Costing::find($item->costing_id);

        return response([
          'data' => [
            'message' => 'Item was deleted successfully.',
            'item' => $item,
            'items' => $this->get_items($item->costing_id, $item->feature_component_id),
            'costing' => $costing
          ]
        ] , Response::HTTP_OK);
    }


    public function copy(Request $request){
      $old_item = CostingItem::find($request->id);
      $new_item = $old_item->replicate();
      $new_item->push();
      $new_item->inventory_part_id = null;
      $new_item->save();

      $this->update_costing_summary_after_modify_item($new_item->costing_id);

      return response([
        'data' => [
          'status' => 'success',
          'message' => 'Item copied successfully',
          'item' => $this->get_item($new_item->costing_item_id)
        ]
      ]);
    }


    public function copy_feature_component_items(Request $request){
      $costing_id = $request->costing_id;
      $from_feature_component_id = $request->from_feature_component_id;
      $to_feature_component_id = $request->to_feature_component_id;
      $to_feature_component = ProductFeatureComponent::find($to_feature_component_id);

      $items = CostingItem::where('costing_id', '=', $costing_id)->where('feature_component_id', '=', $from_feature_component_id)
      ->whereNotIn('inventory_part_id', function ($query) use ($costing_id, $to_feature_component_id) {
        $query->select('inventory_part_id')->from('costing_items')->where('costing_id', '=', $costing_id)->where('feature_component_id', '=', $to_feature_component_id);
      })->get();

      foreach ($items as $item) {
        $new_item = $item->replicate();
        $new_item->feature_component_id = $to_feature_component->feature_component_id;
        $new_item->product_component_id = $to_feature_component->product_component_id;
        $new_item->product_silhouette_id = $to_feature_component->product_silhouette_id;
        $new_item->product_component_line_no = $to_feature_component->line_no;
        $new_item->save();
      }
      $this->update_costing_summary_after_modify_item($costing_id);//update summery

      return response([
        'status' => 'success',
        'message' => 'Items coppied successfully',
        'items' => $this->get_items($costing_id, $to_feature_component_id),
        'costing' => Costing::find($costing_id)
      ]);
    }
    //private function update_finish_good_


    private function generate_item_data($item_data){
      //$item_data['category_id'] = Category::where('category_name', '=', $item_data['category_name'])->first()->category_id;
      //$item_data['inventory_part_id'] = Item::where('master_description', '=', $item_data['master_description'])->first()->master_id;
      $item_data['purchase_uom_id'] = UOM::where('uom_code', '=', $item_data['uom_code'])->first()->uom_id;
      $item_data['origin_type_id'] = OriginType::where('origin_type', '=', $item_data['origin_type'])->first()->origin_type_id;

      //position
      if($item_data['position'] != null && $item_data['position'] != ''){
        $item_data['position_id'] = Position::where('position', '=', $item_data['position'])->first()->position_id;
      }
      else{
        $item_data['position_id'] = null;
      }
      //item color
      /*if($item_data['color_code'] != null && $item_data['color_code'] != ''){
        $item_data['color_id'] = Color::where('color_code', '=', $item_data['color_code'])->first()->color_id;
      }
      else{
        $item_data['color_id'] = null;
      }*/
      //supplier
      if($item_data['supplier_name'] != null && $item_data['supplier_name'] != ''){
        $item_data['supplier_id'] = Supplier::where('supplier_name', '=', $item_data['supplier_name'])->first()->supplier_id;
      }
      else{
        $item_data['supplier_id'] = null;
      }
      //garment options
      if($item_data['garment_options_description'] != null && $item_data['garment_options_description'] != ''){
        $item_data['garment_options_id'] = GarmentOptions::where('garment_options_description', '=', $item_data['garment_options_description'])->first()->garment_options_id;
      }
      else{
        $item_data['garment_options_id'] = null;
      }
      //ship term
      if($item_data['ship_term_description'] != null && $item_data['ship_term_description'] != ''){
        $item_data['ship_term_id'] = ShipmentTerm::where('ship_term_description', '=', $item_data['ship_term_description'])->first()->ship_term_id;
      }
      else{
        $item_data['ship_term_id'] = null;
      }
      //country
      if($item_data['country_description'] != null && $item_data['country_description'] != ''){
        $item_data['country_id'] = Country::where('country_description', '=', $item_data['country_description'])->first()->country_id;
      }
      else{
        $item_data['country_id'] = null;
      }
      return $item_data;
    }


    /*private function get_item($id){
      $item = CostingFinishGoodComponentItem::leftjoin('item_category', 'item_category.category_id', '=', 'costing_finish_good_component_items.category_id')
      ->leftjoin('item_master', 'item_master.master_id', '=', 'costing_finish_good_component_items.master_id')
      ->leftjoin('merc_position', 'merc_position.position_id', '=', 'costing_finish_good_component_items.position_id')
      ->leftjoin('org_uom', 'org_uom.uom_id', '=', 'costing_finish_good_component_items.uom_id')
      ->leftjoin('org_color', 'org_color.color_id', '=', 'costing_finish_good_component_items.color_id')
      ->leftjoin('org_supplier', 'org_supplier.supplier_id', '=', 'costing_finish_good_component_items.supplier_id')
      ->leftjoin('org_origin_type', 'org_origin_type.origin_type_id', '=', 'costing_finish_good_component_items.origin_type_id')
      ->leftjoin('org_garment_options', 'org_garment_options.garment_options_id', '=', 'costing_finish_good_component_items.garment_options_id')
      ->leftjoin('fin_shipment_term', 'fin_shipment_term.ship_term_id', '=', 'costing_finish_good_component_items.ship_term_id')
      ->leftjoin('org_country', 'org_country.country_id', '=', 'costing_finish_good_component_items.country_id')
      ->select('costing_finish_good_component_items.id', 'costing_finish_good_component_items.fg_component_id', 'costing_finish_good_component_items.article_no',
        'costing_finish_good_component_items.unit_price', 'costing_finish_good_component_items.net_consumption', 'costing_finish_good_component_items.wastage',
        'costing_finish_good_component_items.gross_consumption', 'costing_finish_good_component_items.meterial_type', 'costing_finish_good_component_items.freight_charges',
        'costing_finish_good_component_items.mcq', 'costing_finish_good_component_items.surcharge', 'costing_finish_good_component_items.total_cost',
        'costing_finish_good_component_items.ship_mode', 'costing_finish_good_component_items.lead_time', 'costing_finish_good_component_items.comments',
        'item_category.category_name', 'item_master.master_description', 'merc_position.position', 'org_uom.uom_code', 'org_color.color_code','org_color.color_name',
        'org_supplier.supplier_name', 'org_origin_type.origin_type', 'org_garment_options.garment_options_description', 'fin_shipment_term.ship_term_description',
        'org_country.country_description')->where('costing_finish_good_component_items.id', '=', $id)->first();
        return $item;
    }*/

    private function get_item($id){
      $item = CostingItem::leftjoin('item_master', 'item_master.master_id', '=', 'costing_items.inventory_part_id')
      ->leftjoin('item_category', 'item_category.category_id', '=', 'item_master.category_id')
      ->leftjoin('merc_position', 'merc_position.position_id', '=', 'costing_items.position_id')
      ->leftjoin('org_uom', 'org_uom.uom_id', '=', 'costing_items.purchase_uom_id')
      ->leftjoin('org_color', 'org_color.color_id', '=', 'item_master.color_id')
      ->leftjoin('org_supplier', 'org_supplier.supplier_id', '=', 'costing_items.supplier_id')
      ->leftjoin('org_origin_type', 'org_origin_type.origin_type_id', '=', 'costing_items.origin_type_id')
      ->leftjoin('org_garment_options', 'org_garment_options.garment_options_id', '=', 'costing_items.garment_options_id')
      ->leftjoin('fin_shipment_term', 'fin_shipment_term.ship_term_id', '=', 'costing_items.ship_term_id')
      ->leftjoin('org_country', 'org_country.country_id', '=', 'costing_items.country_id')
      ->select('costing_items.costing_item_id','costing_items.inventory_part_id','costing_items.feature_component_id','costing_items.costing_id',
        'item_master.supplier_reference', 'item_master.master_code','item_master.master_description',
        'costing_items.unit_price','costing_items.purchase_price', 'costing_items.net_consumption', 'costing_items.wastage',
        'costing_items.gross_consumption', 'costing_items.meterial_type', 'costing_items.freight_charges',
        'costing_items.mcq', 'costing_items.surcharge', 'costing_items.total_cost',
        'costing_items.ship_mode', 'costing_items.lead_time', 'costing_items.comments',
        'item_category.category_name','item_category.category_code', 'merc_position.position', 'org_uom.uom_code', 'org_color.color_code','org_color.color_name',
        'org_supplier.supplier_name', 'org_origin_type.origin_type', 'org_garment_options.garment_options_description', 'fin_shipment_term.ship_term_description',
        'org_country.country_description')->where('costing_items.costing_item_id', '=', $id)->first();
        //echo json_encode($item);die();
        return $item;
    }

    /*private function get_items($fg_component_id){
      $items = CostingFinishGoodComponentItem::leftjoin('item_category', 'item_category.category_id', '=', 'costing_finish_good_component_items.category_id')
      ->leftjoin('item_master', 'item_master.master_id', '=', 'costing_finish_good_component_items.master_id')
      ->leftjoin('merc_position', 'merc_position.position_id', '=', 'costing_finish_good_component_items.position_id')
      ->leftjoin('org_uom', 'org_uom.uom_id', '=', 'costing_finish_good_component_items.uom_id')
      ->leftjoin('org_color', 'org_color.color_id', '=', 'costing_finish_good_component_items.color_id')
      ->leftjoin('org_supplier', 'org_supplier.supplier_id', '=', 'costing_finish_good_component_items.supplier_id')
      ->leftjoin('org_origin_type', 'org_origin_type.origin_type_id', '=', 'costing_finish_good_component_items.origin_type_id')
      ->leftjoin('org_garment_options', 'org_garment_options.garment_options_id', '=', 'costing_finish_good_component_items.garment_options_id')
      ->leftjoin('fin_shipment_term', 'fin_shipment_term.ship_term_id', '=', 'costing_finish_good_component_items.ship_term_id')
      ->leftjoin('org_country', 'org_country.country_id', '=', 'costing_finish_good_component_items.country_id')
      ->select('costing_finish_good_component_items.id', 'costing_finish_good_component_items.fg_component_id', 'costing_finish_good_component_items.article_no',
        'costing_finish_good_component_items.unit_price', 'costing_finish_good_component_items.net_consumption', 'costing_finish_good_component_items.wastage',
        'costing_finish_good_component_items.gross_consumption', 'costing_finish_good_component_items.meterial_type', 'costing_finish_good_component_items.freight_charges',
        'costing_finish_good_component_items.mcq', 'costing_finish_good_component_items.surcharge', 'costing_finish_good_component_items.total_cost',
        'costing_finish_good_component_items.ship_mode', 'costing_finish_good_component_items.lead_time', 'costing_finish_good_component_items.comments',
        'item_category.category_name', 'item_master.master_description', 'merc_position.position', 'org_uom.uom_code', 'org_color.color_code', 'org_color.color_name',
        'org_supplier.supplier_name', 'org_origin_type.origin_type', 'org_garment_options.garment_options_description', 'fin_shipment_term.ship_term_description',
        'org_country.country_description', DB::raw('false as edited'))->where('costing_finish_good_component_items.fg_component_id', '=', $fg_component_id)->get();
        return $items;
    }*/

    private function get_items($costing_id, $feature_component_id){
      $items = CostingItem::leftjoin('item_master', 'item_master.master_id', '=', 'costing_items.inventory_part_id')
      ->leftjoin('item_category', 'item_category.category_id', '=', 'item_master.category_id')
      ->leftjoin('merc_position', 'merc_position.position_id', '=', 'costing_items.position_id')
      ->leftjoin('org_uom', 'org_uom.uom_id', '=', 'costing_items.purchase_uom_id')
      ->leftjoin('org_color', 'org_color.color_id', '=', 'item_master.color_id')
      ->leftjoin('org_supplier', 'org_supplier.supplier_id', '=', 'costing_items.supplier_id')
      ->leftjoin('org_origin_type', 'org_origin_type.origin_type_id', '=', 'costing_items.origin_type_id')
      ->leftjoin('org_garment_options', 'org_garment_options.garment_options_id', '=', 'costing_items.garment_options_id')
      ->leftjoin('fin_shipment_term', 'fin_shipment_term.ship_term_id', '=', 'costing_items.ship_term_id')
      ->leftjoin('org_country', 'org_country.country_id', '=', 'costing_items.country_id')
      ->select('costing_items.*',
        //'costing_items.inventory_part_id','costing_items.feature_component_id','costing_items.costing_id',
        'item_master.supplier_reference', 'item_master.master_code','item_master.master_description',
        'item_category.category_name','item_category.category_code', 'merc_position.position', 'org_uom.uom_code', 'org_color.color_code','org_color.color_name',
        'org_supplier.supplier_name', 'org_origin_type.origin_type', 'org_garment_options.garment_options_description', 'fin_shipment_term.ship_term_description',
        'org_country.country_description')
        ->where('costing_items.costing_id', '=', $costing_id)
        ->where('costing_items.feature_component_id', '=', $feature_component_id)
        ->orderBy('item_category.category_name', 'ASC')->get();
        //echo json_encode($item);die();
        return $items;
    }


    private function get_finish_good_items($costing_id){
      $items = CostingItem::leftjoin('item_master', 'item_master.master_id', '=', 'costing_items.inventory_part_id')
      ->leftjoin('item_category', 'item_category.category_id', '=', 'item_master.category_id')
      ->leftjoin('merc_position', 'merc_position.position_id', '=', 'costing_items.position_id')
      ->leftjoin('org_uom', 'org_uom.uom_id', '=', 'costing_items.purchase_uom_id')
      ->leftjoin('org_color', 'org_color.color_id', '=', 'item_master.color_id')
      ->leftjoin('org_supplier', 'org_supplier.supplier_id', '=', 'costing_items.supplier_id')
      ->leftjoin('org_origin_type', 'org_origin_type.origin_type_id', '=', 'costing_items.origin_type_id')
      ->leftjoin('org_garment_options', 'org_garment_options.garment_options_id', '=', 'costing_items.garment_options_id')
      ->leftjoin('fin_shipment_term', 'fin_shipment_term.ship_term_id', '=', 'costing_items.ship_term_id')
      ->leftjoin('org_country', 'org_country.country_id', '=', 'costing_items.country_id')
      ->select('costing_items.*',
        'item_master.supplier_reference', 'item_master.master_code','item_master.master_description',
        'item_category.category_name','item_category.category_code', 'merc_position.position', 'org_uom.uom_code', 'org_color.color_code','org_color.color_name',
        'org_supplier.supplier_name', 'org_origin_type.origin_type', 'org_garment_options.garment_options_description', 'fin_shipment_term.ship_term_description',
        'org_country.country_description')
        ->where('costing_items.costing_id', '=', $costing_id)
        ->whereNull('costing_items.feature_component_id')
        ->orderBy('item_category.category_name', 'ASC')->get();
        return $items;
    }




    //****************************************************************************************************************

    /*private function calculate_fg_rm_cost($fg_id){
      $cost = CostingFinishGoodComponentItem::join('costing_finish_good_components', 'costing_finish_good_components.id', '=', 'costing_finish_good_component_items.fg_component_id')
      ->join('costing_finish_goods', 'costing_finish_goods.fg_id', '=', 'costing_finish_good_components.fg_id')
      ->where('costing_finish_goods.fg_id', '=', $fg_id)->sum('costing_finish_good_component_items.total_cost');
      return $cost;
    }*/

    private function calculate_rm_cost($costing_id){
      $cost = CostingItem::where('costing_id', '=', $costing_id)
      ->sum('total_cost');
      return $cost;
    }

    private function calculate_fabric_cost($costing_id){
      $cost = CostingItem::join('item_master', 'item_master.master_id', '=', 'costing_items.inventory_part_id')
      ->join('item_category', 'item_category.category_id', '=', 'item_master.category_id')
      ->where('costing_items.costing_id', '=', $costing_id)
      ->where('item_category.category_code', '=', 'FAB')
      ->sum('costing_items.total_cost');
      return $cost;
    }

    private function calculate_trim_cost($costing_id){
      $cost = CostingItem::join('item_master', 'item_master.master_id', '=', 'costing_items.inventory_part_id')
      ->join('item_category', 'item_category.category_id', '=', 'item_master.category_id')
      ->where('costing_items.costing_id', '=', $costing_id)
      ->where('item_category.category_code', '=', 'TRM')
      ->sum('costing_items.total_cost');
      return $cost;
    }

    private function calculate_elastic_cost($costing_id){
      $cost = CostingItem::join('item_master', 'item_master.master_id', '=', 'costing_items.inventory_part_id')
      ->join('item_category', 'item_category.category_id', '=', 'item_master.category_id')
      ->where('costing_items.costing_id', '=', $costing_id)
      ->where('item_category.category_code', '=', 'ELA')
      ->sum('costing_items.total_cost');
      return $cost;
    }

    private function calculate_packing_cost($costing_id){
      $cost = CostingItem::join('item_master', 'item_master.master_id', '=', 'costing_items.inventory_part_id')
      ->join('item_category', 'item_category.category_id', '=', 'item_master.category_id')
      ->where('costing_items.costing_id', '=', $costing_id)
      ->where('item_category.category_code', '=', 'PAC')
      ->sum('costing_items.total_cost');
      return $cost;
    }

    private function calculate_other_cost($costing_id){
      $cost = CostingItem::join('item_master', 'item_master.master_id', '=', 'costing_items.inventory_part_id')
      ->join('item_category', 'item_category.category_id', '=', 'item_master.category_id')
      ->where('costing_items.costing_id', '=', $costing_id)
      ->where('item_category.category_code', '=', 'OTHER')
      ->sum('costing_items.total_cost');
      return $cost;
    }

    /*private function update_finish_good_after_modify_item($fg_component_id){
      $fg_component = CostingFinishGoodComponent::find($fg_component_id);
      $fg = CostingFinishGood::find($fg_component->fg_id);
      $costing = Costing::find($fg->costing_id);

      $total_fg_rm_cost = $this->calculate_fg_rm_cost($fg->fg_id);
      $fg_finance_cost = ($total_fg_rm_cost * $costing->finance_charges) / 100;
      $fg_total_cost = $total_fg_rm_cost + $costing->labour_cost + $fg_finance_cost + $costing->coperate_cost;//rm cost + labour cost + finance cost + coperate cost

      $fg->total_rm_cost = round($total_fg_rm_cost, 4, PHP_ROUND_HALF_UP ); //round and assign
      $fg->finance_cost = round($fg_finance_cost, 4, PHP_ROUND_HALF_UP ); //round and assign
      $fg->total_cost = round($fg_total_cost, 4, PHP_ROUND_HALF_UP ); //round and assign
      $fg->epm = $fg->calculate_epm($costing->fob, $total_fg_rm_cost, $costing->total_smv);//calculate fg epm
      $fg->np = $fg->calculate_np($costing->fob, $fg_total_cost); //calculate fg np value
      $fg->save();

      if($fg->pack_no == 1){ //update costing header deatils based on first pack
        $costing->total_rm_cost = $fg->total_rm_cost;
        $costing->finance_cost = $fg->finance_cost;
        $costing->total_cost = $fg->total_cost;
        $costing->epm = $fg->epm;
        $costing->np_margine = $fg->np;
        $costing->save();
      }
    }*/

    private function update_costing_summary_after_modify_item($costing_id){
      //$costing_item = CostingItem::find($costing_item_id);
      $costing = Costing::find($costing_id);

      $fabric_cost = $this->calculate_fabric_cost($costing->id);
      $trim_cost = $this->calculate_trim_cost($costing->id);
      $packing_cost = $this->calculate_packing_cost($costing->id);
      $elastic_cost = $this->calculate_elastic_cost($costing->id);
      $other_cost = $this->calculate_other_cost($costing->id);

      $total_rm_cost = $this->calculate_rm_cost($costing->id);
      $finance_cost = ($total_rm_cost * $costing->finance_charges) / 100;
      $total_cost = $total_rm_cost + $costing->labour_cost + $finance_cost + $costing->coperate_cost;//rm cost + labour cost + finance cost + coperate cost
      $epm = $costing->calculate_epm($costing->fob, $total_rm_cost, $costing->total_smv);//calculate fg epm
      $np = $costing->calculate_np($costing->fob, $total_cost); //calculate fg np value

      $costing->total_rm_cost = round($total_rm_cost, 4, PHP_ROUND_HALF_UP ); //round and assign
      $costing->finance_cost = round($finance_cost, 4, PHP_ROUND_HALF_UP ); //round and assign
      $costing->fabric_cost = round($fabric_cost, 4, PHP_ROUND_HALF_UP ); //round and assign
      $costing->trim_cost = round($trim_cost, 4, PHP_ROUND_HALF_UP ); //round and assign
      $costing->packing_cost = round($packing_cost, 4, PHP_ROUND_HALF_UP ); //round and assign
      $costing->elastic_cost = round($elastic_cost, 4, PHP_ROUND_HALF_UP ); //round and assign
      $costing->other_cost = round($other_cost, 4, PHP_ROUND_HALF_UP ); //round and assign
      $costing->total_cost = round($total_cost, 4, PHP_ROUND_HALF_UP ); //round and assign
      $costing->epm = $epm;
      $costing->np_margine = $np;
      $costing->save();
    }

}
