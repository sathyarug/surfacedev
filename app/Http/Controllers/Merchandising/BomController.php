<?php

namespace App\Http\Controllers\Merchandising;

use App\Models\Merchandising\BOMHeader;
use App\Models\Merchandising\BOMDetails;
use App\Models\Merchandising\CustomerOrder;
use App\Models\Merchandising\CustomerOrderDetails;
use App\Models\Merchandising\Costing\Costing;
use App\Models\Merchandising\Costing\CostingFinishGoodComponentItem;
use App\Models\Merchandising\BOMSOAllocation;
use App\Models\Merchandising\MaterialRatio;

use App\Models\Merchandising\StyleCreation;
use App\Models\Org\UOM;
use App\Models\Org\OriginType;
use App\Models\Merchandising\ProductFeature;
use App\Models\Merchandising\ProductComponent;
use App\Models\Merchandising\ProductSilhouette;
use App\Models\Merchandising\Position;
use App\Models\Org\Color;
use App\Models\Org\Supplier;
use App\Models\Org\GarmentOptions;
use App\Models\Finance\ShipmentTerm;
use App\Models\Org\Country;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\Merchandising\Bom\BomService;
use App\Libraries\Approval;
use App\Models\Merchandising\ShopOrderDetail;
use App\Models\Merchandising\Costing\CostingSfgItem;
use App\Models\Merchandising\Item\Item;

class BomController extends Controller
{
    public function index(Request $request)
    {
      $type = $request->type;

      if($type == 'header_data') {//return bom header data
        return response([
          'data' => $this->get_header_data($request->costing_id)
        ]);
      }
      else if($type == 'items') {
        return response([
          'items' => $this->get_items($request->delivery_id)
        ]);
      }
      else if($type == 'bom_item_details') {
        return response([
          'data' => $this->get_bom_item_details($request->bom_detail_id)
        ]);
      }
      else if($type == 'datatable') {
          $data = $request->all();
          $this->datatable_search($data);
      }
      else if($type == 'style_components'){
        $bom_id = $request->bom_id;
        return response([
          'data' => $this->get_style_components($bom_id)
        ]);
      }
      else if($type == 'style_component_silhouettes'){
        $bom_id = $request->bom_id;
        $component = $request->component;
        return response([
          'data' => $this->get_style_component_silhouettes($bom_id, $component)
        ]);
      }
      else if($type == 'semi_finish_goods'){
        $bom_id = $request->bom_id;
        return response([
          'data' => $this->get_semi_finish_goods($bom_id)
        ]);
      }
      else if($type == 'semi_finish_good_details'){
        $sfg_code = $request->sfg_code;
        return response([
          'data' => $this->get_semi_finish_good_details($sfg_code)
        ]);
      }
    }


    public function store(Request $request)
    {
        $items = $request->items;
        $status = true;
        for($x = 0 ; $x < sizeof($items) ; $x++){
          $bom_item = BOMDetails::find($items[$x]['id']);
          $size_wise_count = DB::table('mat_ratio')->where('bom_detail_id', '=', $bom_item->id)->sum('required_qty');
          if($items[$x]['required_qty'] < $size_wise_count) {
            $status = false;
            break;
          }
          else {
            $bom_item->required_qty = $items[$x]['required_qty'];
            $bom_item->bom_unit_price = $items[$x]['bom_unit_price'];
            $bom_item->total_cost = ($bom_item->gross_consumption * $bom_item->required_qty * $bom_item->bom_unit_price) + $bom_item->freight_cost + $bom_item->surcharge;
            $bom_item->save();
          }
        }

        if($status == true) {
          $bom = BomHeader::find($items[0]['bom_id']);
          return response([
            'data' => [
              'status' => 'success',
              'message' => 'Item saved successfully',
              'items' => $this->get_items($bom->delivery_id)
            ]
          ]);
        }
        else {
          return response([
            'data' => [
              'status' => 'error',
              'message' => 'Size wise qty is grater than enterd required qty in line ' . ($x + 1)
            ]
          ]);
        }
    }


    public function show($id)
    {
      $bom = BomHeader::with(['finish_good', 'country'])->find($id);
      $costing = Costing::with(['style'])->find($bom->costing_id);
      //$feature_component_count = ProductFeature::find($costing->style->product_feature_id)->count;
      $feature_components = $this->get_product_feature_components($costing->style->style_id);
      $feature_component_count = sizeof($feature_components);
      //$header_data = $this->get_header_data($bom->costing_id);
      $items = $this->get_items($bom->bom_id);
      return [
        'data' => [
          'bom' => $bom,
          //'header_data' => $header_data,
          'items' => $items,
          'feature_component_count' => $feature_component_count,
          'feature_components' => $feature_components
        ]
      ];
    }


    public function edit(bom_details $bom_details)
    {
        //
    }


    public function update(Request $request, bom_details $bom_details)
    {
        //
    }


    public function destroy(bom_details $bom_details)
    {
        //
    }



    public function save_item(Request $request){
      $request_data = $request->item_data;
      $bom = BOMHeader::find($request_data['bom_id']);
      $costing = Costing::find($bom->costing_id);

      if($costing->status == 'APPROVED'){//can add item
        //check bom material cost wirh costing cost
        $validate_cost = $this->validate_items_cost([$request_data], $costing, $bom);
        if($validate_cost != ''){ //validation fail
          return response([
            'data' => [
              'status' => 'error',
              'message' => $validate_cost
            ]
          ]);
        }

        $item_data = $this->generate_item_data($request_data);
        //  echo json_encode($item_data);die();
        $bom_detail = null;
        if($item_data['bom_detail_id'] <= 0){
          $bom_detail = new BOMDetails();
        }
        else{
          $bom_detail = BOMDetails::find($item_data['bom_detail_id']);
        }

        if($bom_detail->validate($item_data))
        {
          $bom_detail->fill($item_data);
          if($item_data['bom_detail_id'] <= 0){
            $bom_detail->status = 1;
          }
          $bom_detail->costing_id = $bom->costing_id;//set costing id
          $bom_detail->save();

          $this->update_bom_summary_after_modify_item($bom_detail->bom_id);

          $saved_item = $this->get_item($bom_detail->bom_detail_id);
          $saved_item['edited'] = false;
          return response([
            'data' => [
              'status' => 'success',
              'message' => 'BOM item saved successfully',
              'item' => $saved_item
            ]
          ] , Response::HTTP_CREATED );
        }
        else{
          $errors = $costing_item->errors();// failure, get errors
          return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
      }
      else {//cannot add item
        return response([
          'data' => [
            'status' => 'error',
            'message' => 'Cannot add itema to BOM. Costing is not approved.'
          ]
        ]);
      }
    }


    public function save_items(Request $request){
      $items = $request->items;
      if(sizeof($items) > 0){
        $bom = BOMHeader::find($items[0]['bom_id']);
        $costing = Costing::find($bom->costing_id);

        if($costing->status == 'APPROVED') {

          //check bom material cost wirh costing cost
          $validate_cost = $this->validate_items_cost($items, $costing, $bom);
          if($validate_cost != ''){ //validation fail
            return response([
              'data' => [
                'status' => 'error',
                'message' => $validate_cost
              ]
            ]);
          }

          for($x = 0 ; $x < sizeof($items) ; $x++){
            $bom_detail = null;
            if($items[$x]['bom_detail_id'] <= 0){
              $bom_detail = new BOMDetails();
            }
            else{
              $bom_detail = BOMDetails::find($items[$x]['bom_detail_id']);
            }

            $item_data = $this->generate_item_data($items[$x]);

            if($bom_detail->validate($item_data))
            {
              $bom_detail->fill($item_data);
              if($item_data['bom_detail_id'] <= 0){
                $bom_detail->status = 1;
              }
              $bom_detail->save();
            }
            else{
              continue;
            }
          }
          $this->update_bom_summary_after_modify_item($items[0]['bom_id']);
        }
        else {
          return response([
            'data' => [
              'status' => 'errors',
              'message' => 'Cannot save items. Costing not approved.'
            ]
          ]);
        }
      }
      return response([
        'data' => [
          'status' => 'success',
          'message' => 'Items saved successfully',
          'items' => $this->get_items($items[0]['bom_id'])
        ]
      ]);
    }


    public function remove_item(Request $request)
    {
        $bom_detail_id = $request->bom_detail_id;
        $shop_order_item = ShopOrderDetail::where('bom_detail_id', '=', $bom_detail_id)->first();

        if($shop_order_item != null && $shop_order_item['po_con'] == 'CREATE'){
          return response([
            'data' => [
              'status' => 'error',
              'message' => 'Cannot remove item. Purchase order already created.'
            ] ]);
        }
        else {
          $item = BOMDetails::find($bom_detail_id);
          $item->delete();
          $this->update_bom_summary_after_modify_item($item->bom_id);

          return response([
            'data' => [
              'status' => 'success',
              'message' => 'Item was deleted successfully.',
              'item' => $item,
              'items' => $this->get_items($item->bom_id)
            ]
          ] , Response::HTTP_OK);
        }
    }


    public function copy_item(Request $request){
      $old_item = BOMDetails::find($request->bom_detail_id);
      $new_item = $old_item->replicate();
      $new_item->push();
      $new_item->inventory_part_id = null;
      $new_item->save();

      $this->update_bom_summary_after_modify_item($new_item->bom_id);

      return response([
        'data' => [
          'status' => 'success',
          'message' => 'Item copied successfully',
          'item' => $this->get_item($new_item->bom_detail_id)
        ]
      ]);
    }



    public function copy_all_items_from(Request $request){
      $from_bom_id = $request->from_bom_id;
      $to_bom_id = $request->to_bom_id;

      $from_bom = BOMHeader::find($from_bom_id);
      $to_bom = BOMHeader::find($to_bom_id);
      $from_bom_costing = Costing::find($from_bom->costing_id);
      $to_bom_costing = Costing::find($to_bom->costing_id);

      if($from_bom_costing->style_id != $to_bom_costing->style_id ){
        return response([
          'status' => 'error',
          'message' => 'Cannot copy items between different styles'
        ]);
      }
      else {

        $sfg_items = CostingSfgItem::select('costing_sfg_item.*', 'item_master.master_code')
        ->join('costing_fng_item', 'costing_fng_item.costing_fng_id', '=', 'costing_sfg_item.costing_fng_id')
        ->join('item_master', 'item_master.master_id', '=', 'costing_sfg_item.sfg_id')
        ->where('costing_fng_item.costing_id', '=', $to_bom->costing_id)
        ->where('costing_fng_item.fng_id', '=', $to_bom->fng_id)
        ->get();//get costing sfg items

        if(sizeof($sfg_items) > 1){ //mult pack

          foreach($sfg_items as $sfg_item) {
            $items = BOMDetails::select('bom_details.*')
            ->leftjoin('bom_details AS bom_details_to', function($join) use($to_bom_id){
              $join->on('bom_details_to.product_component_id', '=', 'bom_details.product_component_id')
              ->on('bom_details_to.product_silhouette_id', '=', 'bom_details.product_silhouette_id')
              ->on('bom_details_to.product_component_line_no', '=', 'bom_details.product_component_line_no')
              ->on('bom_details_to.inventory_part_id', '=', 'bom_details.inventory_part_id')
              ->where('bom_details_to.bom_id', '=', $to_bom_id);
            })
            ->where('bom_details.bom_id', '=', $from_bom_id)
            //->where('bom_details_to.bom_id', '=', $to_bom_id)
            ->where('bom_details.product_component_id', '=', $sfg_item->product_component_id)
            ->where('bom_details.product_silhouette_id', '=', $sfg_item->product_silhouette_id)
            ->where('bom_details.product_component_line_no', '=', $sfg_item->product_component_line_no)
            ->whereNull('bom_details_to.bom_detail_id')->get();

            foreach ($items as $item) {
              $new_item = $item->replicate();
              $new_item->bom_id = $to_bom_id;
              $new_item->costing_item_id = null;
              $new_item->costing_id = $to_bom->costing_id;
              $new_item->sfg_id = $sfg_item->sfg_id;
              $new_item->sfg_code = $sfg_item->master_code;
              $new_item->save();
            }
          }
        }
        else { //single pack
          $items = BOMDetails::select('bom_details.*')
          ->leftjoin('bom_details AS bom_details_to', function($join) use($to_bom_id){
            $join->on('bom_details_to.inventory_part_id', '=', 'bom_details.inventory_part_id')
            ->where('bom_details_to.bom_id', '=', $to_bom_id);
          })
          ->where('bom_details.bom_id', '=', $from_bom_id)
          ->whereNull('bom_details_to.bom_detail_id')->get();

          foreach ($items as $item) {
            $new_item = $item->replicate();
            $new_item->bom_id = $to_bom_id;
            $new_item->costing_item_id = null;
            $new_item->costing_id = $to_bom->costing_id;
            $new_item->save();
          }
        }
        $this->update_bom_summary_after_modify_item($to_bom_id);//update summery

        return response([
          'status' => 'success',
          'message' => 'Items coppied successfully',
          'items' => $this->get_items($to_bom_id),
          'bom' => BomHeader::find($to_bom_id)
        ]);
      }
    }



    public function edit_mode(Request $request){
      $bom_id = $request->bom_id;
      $edit_status = $request->edit_status;
      $bom = BOMHeader::find($bom_id);

      if($bom != null){ //has a bom
        $costing = Costing::find($bom->costing_id);

        if($edit_status == 1){//put to edit status
            $user_id = auth()->user()->user_id;

            if($bom->edit_status == 1 && $bom->created_by == $user_id){//already in edit mode
              //chek costing

              if($costing->edit_status == 1){ //costing is in edit mode, cannot update bom
                return response([
                  'status' => 'error',
                  'message' => "Cannot edit bom. Costing is in edit mode."
                ]);
              }
              else if($costing->status != 'APPROVED'){
                return response([
                  'status' => 'error',
                  'message' => "Cannot edit bom. Costing is not approved"
                ]);
              }
              else {
                return response([
                  'status' => 'success',
                  'message' => "You can edit costing"
                ]);
              }
            }
            else if($bom->edit_status == 1 && $bom->created_by != $user_id){
              return response([
                'status' => 'error',
                'message' => "You cannot edit bom. It's already in edit mode"
              ]);
            }
            else {
              if($costing->status == 'APPROVED'){
                if($bom->created_by == $user_id) {//costing created user and can edit
                  $bom->edit_status = 1;
                  $bom->edit_user = $user_id;
                  $bom->save();
                  //add costing to edit mode
                  /*$costing->edit_status = 1;
                  $costing->edit_user = $user_id;
                  $costing->save();*/

                  return response([
                    'status' => 'success',
                    'message' => "You can edit bom"
                  ]);
                }
                else {
                  return response([
                    'status' => 'error',
                    'message' => "Only Bom created user can edit the Bom"
                  ]);
                }
              }
              else {
                return response([
                  'status' => 'error',
                  'message' => "Cannot edit bom. Costing is not approved"
                ]);
              }
            }
        }
        else {//exit from edit mode
          $user_id = auth()->user()->user_id;
          if($bom->edit_status == 1 && $bom->edit_user == $user_id){//can edit
            $bom->edit_status = 0;
            $bom->edit_user = null;
            $bom->save();
            //remove costing from edit mode
            /*$costing->edit_status = 0;
            $costing->edit_user = null;
            $costing->save();*/

            return response([
              'status' => 'success',
              'message' => "Bom removed from edit status"
            ]);
          }
          else {
            return response([
              'status' => 'error',
              'message' => "Costing is not in the edit status or user don't have permissions to edit costing"
            ]);
          }
        }
      }
      else {//no costing
        return response([
          'status' => 'error',
          'message' => "Incorrect Bom"
        ]);
      }
    }



    public function confirm_bom(Request $request){
        $bom_id = $request->bom_id;
        $bom = BOMHeader::find($bom_id);
        $bomService = new BomService();

        if($bom->edit_status == 1){
          //check epm and np margin
          $need_approval = $bomService->is_bom_need_approval($bom_id);

          $bomService->save_bom_revision($bom_id);
          $bom->edit_status = 0;
          $bom->edit_user = null;

          if($need_approval == true){ //need to send for approval
            $bom->status = 'CONFIRM';
            $bom->save();
          }
          else {
            $bom->status = 'RELEASED';
            $bom->save();
            //create new shop order items
            $bom_service = new BomService();
            $bom_service->create_shop_order_items($bom_id);//create new shop order items
          }

          $bom = BOMHeader::find($bom_id);//get updated bom header

          return response([
            'status' => 'success',
            'message' => "Bom confirmed successfully.",
            'bom' => $bom
          ]);
        }
        else {
          return response([
            'status' => 'error',
            'message' => "Bom not in edit mode."
          ]);
        }
    }



    public function send_for_approval(Request $request){
        $bom_id = $request->bom_id;
        $bom = BOMHeader::find($bom_id);

        if($bom->status == 'CONFIRM') {
          //live code
          /*$bom->status = 'PENDING';
          $bom->save();
          $approval = new Approval();
          $approval->start('BOM', $bom->bom_id, $bom->created_by);//start costing approval process*/

          //test code without approval, need to remove in live mode
          $bom->status = 'RELEASED';
          $bom->save();
          $bom_service = new BomService();
          $bom_service->create_shop_order_items($bom_id);//create new shop order items

          return response([
            'status' => 'success',
            'message' => "Bom send for approval successfully.",
            'bom' => $bom
          ]);
        }
        else {
          return response([
            'status' => 'error',
            'message' => "Bom not in confirm status."
          ]);
        }
    }

    //..........................................................................

    private function validate_items_cost($items, $costing, $bom){
      $fabric_cost = $bom->fabric_cost;
      $trim_cost = $bom->trim_cost;
      $packing_cost = $bom->packing_cost;
      $elastic_cost = $bom->elastic_cost;
      $other_cost = $bom->other_cost;

      foreach($items as $item){
        //check is a new item or edited item_master
        if($item['bom_detail_id'] == 0) {//new item

          if($item['category_code'] == 'FAB'){
            $fabric_cost += $item['total_cost'];
          }
          else if($item['category_code'] == 'TRM'){
            $trim_cost += $item['total_cost'];
          }
          else if($item['category_code'] == 'PAC'){
            $packing_cost += $item['total_cost'];
          }
          else if($item['category_code'] == 'ELA'){
            $elastic_cost += $item['total_cost'];
          }
          else if($item['category_code'] == 'OT'){
            $other_cost += $item['total_cost'];
          }
        }
        else {//update item
          $bom_detail = BOMDetails::find($item['bom_detail_id']);

          if($item['category_code'] == 'FAB'){
            $fabric_cost += ($item['total_cost'] - $bom_detail->total_cost);
          }
          else if($item['category_code'] == 'TRM'){
            $trim_cost += ($item['total_cost'] - $bom_detail->total_cost);
          }
          else if($item['category_code'] == 'PAC'){
            $packing_cost += ($item['total_cost'] - $bom_detail->total_cost);
          }
          else if($item['category_code'] == 'ELA'){
            $elastic_cost += ($item['total_cost'] - $bom_detail->total_cost);
          }
          else if($item['category_code'] == 'OT'){
            $other_cost += ($item['total_cost'] - $bom_detail->total_cost);
          }
        }
      }
      //echo json_encode($fabric_cost);die();
      $message = '';
      if($fabric_cost > $costing->fabric_cost){
        $message .= 'BOM fabric cost is grater than costing fabric cost. ';
      }
      if($trim_cost > $costing->trim_cost){
        $message .= 'BOM trim cost is grater than costing trim cost. ';
      }
      if($packing_cost > $costing->packing_cost){
        $message .= 'BOM packing cost is grater than costing packing cost. ';
      }
      if($elastic_cost > $costing->elastic_cost){
        $message .= 'BOM elastic cost is grater than costing elastic cost. ';
      }
      if($other_cost > $costing->other_cost){
        $message .= 'BOM other cost is grater than costing other cost. ';
      }
      return  $message;
    }


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
      //semi finish good
      if($item_data['sfg_code'] != null && $item_data['sfg_code'] != ''){
        $item_data['sfg_id'] = Item::where('master_code', '=', $item_data['sfg_code'])->first()->master_id;
      }
      else {
        $item_data['sfg_id'] = null;
        $item_data['sfg_code'] = null;
      }
      //product component
      /*if($item_data['product_component_description'] != null && $item_data['product_component_description'] != ''){
        $item_data['product_component_id'] = ProductComponent::where('product_component_description', '=', $item_data['product_component_description'])->first()->product_component_id;
      }
      else{
        $item_data['product_component_id'] = null;
      }*/
      //product silhuatte
      /*if($item_data['product_silhouette_description'] != null && $item_data['product_silhouette_description'] != ''){
        $item_data['product_silhouette_id'] = ProductSilhouette::where('product_silhouette_description', '=', $item_data['product_silhouette_description'])->first()->product_silhouette_id;
      }
      else{
        $item_data['product_silhouette_id'] = null;
      }*/

      return $item_data;
    }


    private function update_bom_summary_after_modify_item($bom_id){
      //$costing_item = CostingItem::find($costing_item_id);
      $bom = BomHeader::find($bom_id);

      $fabric_cost = $this->calculate_fabric_cost($bom->bom_id);
      $trim_cost = $this->calculate_trim_cost($bom->bom_id);
      $packing_cost = $this->calculate_packing_cost($bom->bom_id);
      $elastic_cost = $this->calculate_elastic_cost($bom->bom_id);
      $other_cost = $this->calculate_other_cost($bom->bom_id);

      $total_rm_cost = $this->calculate_rm_cost($bom->bom_id);
      $finance_cost = ($total_rm_cost * $bom->finance_charges) / 100;
      $total_cost = $total_rm_cost + $bom->labour_cost + $finance_cost + $bom->coperate_cost;//rm cost + labour cost + finance cost + coperate cost
      $epm = $bom->calculate_epm($bom->fob, $total_rm_cost, $bom->total_smv);//calculate fg epm
      $np = $bom->calculate_np($bom->fob, $total_cost); //calculate fg np value

      $bom->total_rm_cost = round($total_rm_cost, 4, PHP_ROUND_HALF_UP ); //round and assign
      $bom->finance_cost = round($finance_cost, 4, PHP_ROUND_HALF_UP ); //round and assign
      $bom->fabric_cost = round($fabric_cost, 4, PHP_ROUND_HALF_UP ); //round and assign
      $bom->trim_cost = round($trim_cost, 4, PHP_ROUND_HALF_UP ); //round and assign
      $bom->packing_cost = round($packing_cost, 4, PHP_ROUND_HALF_UP ); //round and assign
      $bom->elastic_cost = round($elastic_cost, 4, PHP_ROUND_HALF_UP ); //round and assign
      $bom->other_cost = round($other_cost, 4, PHP_ROUND_HALF_UP ); //round and assign
      $bom->total_cost = round($total_cost, 4, PHP_ROUND_HALF_UP ); //round and assign
      $bom->epm = $epm;
      $bom->np_margin = $np;
      $bom->save();
    }


    private function calculate_rm_cost($bom_id){
      $cost = BOMDetails::where('bom_id', '=', $bom_id)
      ->sum('total_cost');
      return $cost;
    }


    private function calculate_fabric_cost($bom_id){
      $cost = BOMDetails::join('item_master', 'item_master.master_id', '=', 'bom_details.inventory_part_id')
      ->join('item_category', 'item_category.category_id', '=', 'item_master.category_id')
      ->where('bom_details.bom_id', '=', $bom_id)
      ->where('item_category.category_code', '=', 'FAB')
      ->sum('bom_details.total_cost');
      return $cost;
    }

    private function calculate_trim_cost($bom_id){
      $cost = BOMDetails::join('item_master', 'item_master.master_id', '=', 'bom_details.inventory_part_id')
      ->join('item_category', 'item_category.category_id', '=', 'item_master.category_id')
      ->where('bom_details.costing_id', '=', $bom_id)
      ->where('item_category.category_code', '=', 'TRM')
      ->sum('bom_details.total_cost');
      return $cost;
    }

    private function calculate_packing_cost($bom_id){
      $cost = BOMDetails::join('item_master', 'item_master.master_id', '=', 'bom_details.inventory_part_id')
      ->join('item_category', 'item_category.category_id', '=', 'item_master.category_id')
      ->where('bom_details.bom_id', '=', $bom_id)
      ->where('item_category.category_code', '=', 'PAC')
      ->sum('bom_details.total_cost');
      return $cost;
    }

    private function calculate_elastic_cost($bom_id){
      $cost = BOMDetails::join('item_master', 'item_master.master_id', '=', 'bom_details.inventory_part_id')
      ->join('item_category', 'item_category.category_id', '=', 'item_master.category_id')
      ->where('bom_details.bom_id', '=', $bom_id)
      ->where('item_category.category_code', '=', 'ELA')
      ->sum('bom_details.total_cost');
      return $cost;
    }

    private function calculate_other_cost($bom_id){
      $cost = BOMDetails::join('item_master', 'item_master.master_id', '=', 'bom_details.inventory_part_id')
      ->join('item_category', 'item_category.category_id', '=', 'item_master.category_id')
      ->where('bom_details.bom_id', '=', $bom_id)
      ->where('item_category.category_code', '=', 'OTHER')
      ->sum('bom_details.total_cost');
      return $cost;
    }


    private function get_item($id){
      $item = BOMDetails::leftjoin('item_master', 'item_master.master_id', '=', 'bom_details.inventory_part_id')
      ->leftjoin('item_category', 'item_category.category_id', '=', 'item_master.category_id')
      ->leftjoin('merc_position', 'merc_position.position_id', '=', 'bom_details.position_id')
      ->leftjoin('org_uom', 'org_uom.uom_id', '=', 'bom_details.purchase_uom_id')
      ->leftjoin('org_color', 'org_color.color_id', '=', 'item_master.color_id')
      ->leftjoin('org_supplier', 'org_supplier.supplier_id', '=', 'bom_details.supplier_id')
      ->leftjoin('org_origin_type', 'org_origin_type.origin_type_id', '=', 'bom_details.origin_type_id')
      ->leftjoin('org_garment_options', 'org_garment_options.garment_options_id', '=', 'bom_details.garment_options_id')
      ->leftjoin('fin_shipment_term', 'fin_shipment_term.ship_term_id', '=', 'bom_details.ship_term_id')
      ->leftjoin('org_country', 'org_country.country_id', '=', 'bom_details.country_id')
      ->leftJoin('product_component', 'product_component.product_component_id', '=', 'bom_details.product_component_id')
      ->leftJoin('product_silhouette', 'product_silhouette.product_silhouette_id', '=', 'bom_details.product_silhouette_id')
      ->select('bom_details.*',
        /*'bom_details.inventory_part_id','bom_details.feature_component_id','bom_details.costing_id','bom_details.bom_id',
        'bom_details.bom_unit_price', 'bom_details.net_consumption', 'bom_details.wastage',
        'bom_details.gross_consumption', 'bom_details.freight_charges',
        'bom_details.mcq', 'bom_details.surcharge', 'bom_details.total_cost',
        'bom_details.ship_mode', 'bom_details.lead_time', 'bom_details.comments',
        */
        'item_master.supplier_reference', 'item_master.master_code','item_master.master_description',
        'item_category.category_name','item_category.category_code', 'merc_position.position', 'org_uom.uom_code', 'org_color.color_code','org_color.color_name',
        'org_supplier.supplier_name', 'org_origin_type.origin_type', 'org_garment_options.garment_options_description', 'fin_shipment_term.ship_term_description',
        'org_country.country_description','product_component.product_component_description','product_silhouette.product_silhouette_description')
        ->where('bom_details.bom_detail_id', '=', $id)->first();
        //echo json_encode($item);die();
        return $item;
    }






    private function get_product_feature_components($style_id){
      $product_feature_components = DB::select("SELECT
        product_feature.product_feature_id,
        product_feature.product_feature_description,
        product_component.product_component_id,
        product_component.product_component_description,
        product_silhouette.product_silhouette_id,
        product_silhouette.product_silhouette_description,
        product_feature_component.feature_component_id,
        product_feature_component.line_no
        FROM product_feature_component
        INNER JOIN product_feature ON product_feature.product_feature_id = product_feature_component.product_feature_id
        INNER JOIN product_silhouette ON product_silhouette.product_silhouette_id = product_feature_component.product_silhouette_id
        INNER JOIN product_component ON product_component.product_component_id = product_feature_component.product_component_id
        INNER JOIN style_creation ON style_creation.product_feature_id = product_feature.product_feature_id
        WHERE style_creation.style_id = ?", [$style_id]);

        return $product_feature_components;
    }



    private function get_semi_finish_goods($bom_id){
      $sfg_list = CostingSfgItem::select('item_master.master_code')
      ->join('costing_fng_item', 'costing_fng_item.costing_fng_id', '=', 'costing_sfg_item.costing_fng_id')
      ->join('bom_header', 'bom_header.fng_id', '=', 'costing_fng_item.fng_id')
      ->join('item_master', 'item_master.master_id', '=', 'costing_sfg_item.sfg_id')
      ->where('bom_header.bom_id', '=', $bom_id)->get()->pluck('master_code');
      return $sfg_list;
    }


    private function get_semi_finish_good_details($sfg_code){
      $details = CostingSfgItem::select('product_component.product_component_id', 'product_component.product_component_description',
        'product_silhouette.product_silhouette_id', 'product_silhouette.product_silhouette_description', 'costing_sfg_item.product_component_line_no')
      ->join('item_master', 'item_master.master_id', '=', 'costing_sfg_item.sfg_id')
      ->join('product_component', 'product_component.product_component_id', '=', 'costing_sfg_item.product_component_id')
      ->join('product_silhouette', 'product_silhouette.product_silhouette_id', '=', 'costing_sfg_item.product_silhouette_id')
      ->where('item_master.master_code', '=', $sfg_code)->first();
      return $details;
    }


    private function get_style_components($bom_id){
      $components = BOMHeader::select('product_component.product_component_description')
      ->join('costing', 'costing.id', '=', 'bom_header.costing_id')
      ->join('style_creation', 'style_creation.style_id', '=', 'costing.style_id')
      ->join('product_feature', 'product_feature.product_feature_id', '=', 'style_creation.product_feature_id')
      ->join('product_feature_component', 'product_feature_component.product_feature_id', '=', 'product_feature.product_feature_id')
      ->join('product_component', 'product_component.product_component_id', '=', 'product_feature_component.product_component_id')
      ->where('bom_header.bom_id', '=', $bom_id)->get()->pluck('product_component_description');
      /*$components = DB::select("SELECT product_component.product_component_description FROM bom_header
        INNER JOIN costing ON costing.id = bom_header.costing_id
        INNER JOIN style_creation ON style_creation.style_id = costing.style_id
        INNER JOIN product_feature ON product_feature.product_feature_id = style_creation.product_feature_id
        INNER JOIN product_feature_component ON product_feature_component.product_feature_id = product_feature.product_feature_id
        INNER JOIN product_component ON product_component.product_component_id = product_feature_component.product_component_id
        WHERE bom_header.bom_id = ?", [$bom_id])->pluck('product_component_description');;*/
      return $components;
    }


    private function get_style_component_silhouettes($bom_id, $component){
      $silhouettes = BOMHeader::select('product_silhouette.product_silhouette_description')
      ->join('costing', 'costing.id', '=', 'bom_header.costing_id')
      ->join('style_creation', 'style_creation.style_id', '=', 'costing.style_id')
      ->join('product_feature', 'product_feature.product_feature_id', '=', 'style_creation.product_feature_id')
      ->join('product_feature_component', 'product_feature_component.product_feature_id', '=', 'product_feature.product_feature_id')
      ->join('product_component', 'product_component.product_component_id', '=', 'product_feature_component.product_component_id')
      ->join('product_silhouette', 'product_silhouette.product_silhouette_id', '=', 'product_feature_component.product_silhouette_id')
      ->where('bom_header.bom_id', '=', $bom_id)
      ->where('product_component.product_component_description', '=', $component)
      ->get()->pluck('product_silhouette_description');
      return $silhouettes;
    }



//******************************************************

 public function saveMeterialRatio(Request $request){
   $ratio = $request->ratio;
   $bom_detail = BOMDetails::find($request->bom_detail_id);
   //must check ratio is used in purchase order
   MaterialRatio::where('bom_detail_id', '=', $bom_detail->id)->delete();

   for($x = 0 ; $x < sizeof($ratio) ; $x++){
     $mat_ratio = new MaterialRatio();
     $mat_ratio->bom_id = $bom_detail->bom_id;
     $mat_ratio->bom_detail_id = $bom_detail->id;
     $mat_ratio->color_id = $ratio[$x]['color_id'];
     $mat_ratio->size_id = $ratio[$x]['size_id'];
     $mat_ratio->required_qty = $ratio[$x]['required_qty'];
     $mat_ratio->status = 1;
     $mat_ratio->save();
   }

   return response([
     'data' => [
       'status' => 'success',
       'message' => 'Material ratio saved successfully',
       'ratio' => $this->get_mat_ratio($bom_detail->id)
     ]
   ]);
 }


//*******************************************************


private function get_header_data($costing_id){
  $costing = Costing::with(['bom_stage', 'season', 'color_type'])->find($costing_id);
  $style = StyleCreation::with(['customer', 'division'])->find($costing->style_id);
  //$deliveries = $this->get_costing_connected_deliveries($costing_id);
  return [
    'style_id' => $style->style_id,
    'style_no' => $style->style_no,
    'style_description' => $style->style_description,
    'bom_stage_id' => $costing->bom_stage->bom_stage_id,
    'bom_stage_description' => $costing->bom_stage->bom_stage_description,
    'season_id' => $costing->season->season_id,
    'season_name' => $costing->season->season_name,
    'col_opt_id' => $costing->color_type->col_opt_id,
    'color_option' => $costing->color_type->color_option,
    'customer_id' => $style->customer->customer_id,
    'customer_name' => $style->customer->customer_name,
    'division_id' => $style->division->division_id,
    'division_description' => $style->division->division_description,
    'order_code' => (sizeof($deliveries) > 0) ? $deliveries[0]->order_code : '',
    'deliveries' => $deliveries
  ];
}


private function get_costing_connected_deliveries($costing_id){
    $list = CustomerOrderDetails::select('merc_customer_order_details.details_id', 'merc_customer_order_details.line_no',
    'merc_customer_order_header.order_code', 'merc_customer_order_details.po_no', 'merc_customer_order_details.planned_qty',
    'merc_customer_order_details.order_qty',"org_country.country_description", "merc_customer_order_details.cus_style_manual")
    ->join('merc_customer_order_header', 'merc_customer_order_header.order_id', '=', 'merc_customer_order_details.order_id')
    ->join('org_country', 'org_country.country_id', '=', 'merc_customer_order_details.country')
    ->where('merc_customer_order_details.costing_id', '=', $costing_id)->where('merc_customer_order_details.delivery_status', '=', 'CONNECTED')
    ->get();
    return $list;
}


/*private function get_items($delivery_id) {
  $delivery = CustomerOrderDetails::find($delivery_id);

  $items = BOMDetails::join('bom_header', 'bom_header.bom_id', '=', 'bom_details.bom_id')
  ->join('costing_finish_good_components', 'costing_finish_good_components.id', '=', 'bom_details.fg_component_id')
  ->join('product_component', 'product_component.product_component_id', '=', 'costing_finish_good_components.product_component_id')
  ->leftjoin('item_category', 'item_category.category_id', '=', 'bom_details.category_id')
  ->leftjoin('item_master', 'item_master.master_id', '=', 'bom_details.master_id')
  ->leftjoin('merc_position', 'merc_position.position_id', '=', 'bom_details.position_id')
  ->leftjoin('org_uom', 'org_uom.uom_id', '=', 'bom_details.uom_id')
  ->leftjoin('org_color', 'org_color.color_id', '=', 'bom_details.color_id')
  ->leftjoin('org_supplier', 'org_supplier.supplier_id', '=', 'bom_details.supplier_id')
  ->leftjoin('org_origin_type', 'org_origin_type.origin_type_id', '=', 'bom_details.origin_type_id')
  ->leftjoin('org_garment_options', 'org_garment_options.garment_options_id', '=', 'bom_details.garment_options_id')
  ->leftjoin('fin_shipment_term', 'fin_shipment_term.ship_term_id', '=', 'bom_details.ship_term_id')
  ->leftjoin('org_country', 'org_country.country_id', '=', 'bom_details.country_id')
  ->select('bom_details.*', 'item_category.category_name', 'item_master.master_description','item_master.subcategory_id', 'merc_position.position',
      'org_uom.uom_code', 'org_color.color_name', 'org_supplier.supplier_name', 'org_origin_type.origin_type',
      'org_garment_options.garment_options_description', 'fin_shipment_term.ship_term_description', 'org_country.country_description',
      'product_component.product_component_description','product_component.product_component_id',
      DB::raw('false as edited')
   )->where('bom_header.delivery_id', '=', $delivery->details_id)->get();
  return $items;
}*/

private function get_items($bom_id){
  $items = BOMDetails::leftjoin('item_master', 'item_master.master_id', '=', 'bom_details.inventory_part_id')
  ->leftjoin('item_category', 'item_category.category_id', '=', 'item_master.category_id')
  ->leftjoin('merc_position', 'merc_position.position_id', '=', 'bom_details.position_id')
  ->leftjoin('org_uom', 'org_uom.uom_id', '=', 'bom_details.purchase_uom_id')
  ->leftjoin('org_color', 'org_color.color_id', '=', 'item_master.color_id')
  ->leftjoin('org_supplier', 'org_supplier.supplier_id', '=', 'bom_details.supplier_id')
  ->leftjoin('org_origin_type', 'org_origin_type.origin_type_id', '=', 'bom_details.origin_type_id')
  ->leftjoin('org_garment_options', 'org_garment_options.garment_options_id', '=', 'bom_details.garment_options_id')
  ->leftjoin('fin_shipment_term', 'fin_shipment_term.ship_term_id', '=', 'bom_details.ship_term_id')
  ->leftjoin('org_country', 'org_country.country_id', '=', 'bom_details.country_id')
  ->leftjoin('product_component', 'product_component.product_component_id', '=', 'bom_details.product_component_id')
  ->leftjoin('product_silhouette', 'product_silhouette.product_silhouette_id', '=', 'bom_details.product_silhouette_id')
  ->select('bom_details.*','item_master.supplier_reference', 'item_master.master_code','item_master.master_description',
  /*->select('bom_details.costing_item_id','bom_details.inventory_part_id','bom_details.costing_id',*/
    /*'bom_details.bom_unit_price', 'bom_details.net_consumption', 'bom_details.wastage',
    'bom_details.gross_consumption', 'bom_details.meterial_type', 'bom_details.freight_charges',
    'bom_details.mcq', 'bom_details.surcharge', 'bom_details.total_cost',
    'bom_details.ship_mode', 'bom_details.lead_time', 'bom_details.comments',*/
    'item_category.category_name','item_category.category_code', 'merc_position.position', 'org_uom.uom_code', 'org_color.color_code','org_color.color_name',
    'org_supplier.supplier_name', 'org_origin_type.origin_type', 'org_garment_options.garment_options_description', 'fin_shipment_term.ship_term_description',
    'org_country.country_description','product_component.product_component_description','product_silhouette.product_silhouette_description')
    ->where('bom_details.bom_id', '=', $bom_id)->orderBy('bom_details.sfg_code', 'ASC')
    ->orderBy('bom_details.product_component_id', 'ASC')
    ->orderBy('bom_details.product_silhouette_id', 'ASC')
    ->orderBy('item_category.category_name', 'ASC')->get();
    //echo json_encode($item);die();
    return $items;
}


private function generate_bom_for_delivery($delivery_id) {
  $delivery = CustomerOrderDetails::find($delivery_id);
  //$costing_finisg_good = CostingFinishGood::find($delivery->fg_id);

  $bom = new BomHeader();
  $bom->costing_id = $delivery->costing_id;
  $bom->delivery_id = $delivery->details_id;
  $bom->save();

  $components = CostingFinishGoodComponent::where('fg_id', '=', $delivery->fg_id)->get()->pluck('id');
  $items = CostingFinishGoodComponentItem::whereIn('fg_component_id', $components)->get();
  $items = json_decode(json_encode($items)); //conver to array
  for($x = 0 ; $x < sizeof($items); $x++) {
    $items[$x]['bom_id'] = $bom->bom_id;
    $items[$x]['costing_item_id'] = $items[$x]['id'];
    $items[$x]['id'] = 0; //clear id of previous data, will be auto generated
    $items[$x]['created_date'] = null;
    $items[$x]['created_by'] = null;
    $items[$x]['updated_date'] = null;
    $items[$x]['updated_by'] = null;
  }
  DB::table('bom_details')->insert($items);
}


private function get_mat_ratio($bom_detail_id){
  $ratio = MaterialRatio::select('mat_ratio.*', 'org_size.size_name', 'org_color.color_name')
  ->leftjoin('org_size', 'org_size.size_id', '=', 'mat_ratio.size_id')
  ->leftjoin('org_color', 'org_color.color_id', '=', 'mat_ratio.color_id')
  ->where('mat_ratio.bom_detail_id', '=', $bom_detail_id)->get();
  return $ratio;
}


private function get_bom_item_details($bom_detail_id){
  $bom_item = BOMDetails::find($bom_detail_id);
  $ratio = $this->get_mat_ratio($bom_detail_id);
  return [
    'bom_item' => $bom_item,
    'ratio' => $ratio
  ];
}


private function datatable_search($data){
  $start = $data['start'];
  $length = $data['length'];
  $draw = $data['draw'];
  $search = $data['search']['value'];
  $order = $data['order'][0];
  $order_column = $data['columns'][$order['column']]['data'];
  $order_type = $order['dir'];

  $bom_list = BomHeader::select('bom_header.*','style_creation.style_no','merc_bom_stage.bom_stage_description',
    'org_season.season_name', 'merc_color_options.color_option','item_master.master_code')
  ->join('costing', 'costing.id', '=', 'bom_header.costing_id')
  ->join('style_creation', 'style_creation.style_id', '=', 'costing.style_id')
  ->join('merc_bom_stage', 'merc_bom_stage.bom_stage_id', '=', 'costing.bom_stage_id')
  ->join('org_season', 'org_season.season_id', '=', 'costing.season_id')
  ->join('merc_color_options', 'merc_color_options.col_opt_id', '=', 'costing.color_type_id')
  ->join('item_master', 'item_master.master_id', '=', 'bom_header.fng_id')
  ->where('bom_header.bom_id'  , 'like', $search.'%' )
  ->orWhere('item_master.master_code'  , 'like', $search.'%' )
  ->orWhere('style_creation.style_no'  , 'like', $search.'%' )
  ->orWhere('merc_bom_stage.bom_stage_description','like',$search.'%')
  ->orWhere('org_season.season_name','like',$search.'%')
  ->orWhere('merc_color_options.color_option','like',$search.'%')
  ->orderBy($order_column, $order_type)
  ->offset($start)->limit($length)->get();

  $bom_count = BomHeader::join('costing', 'costing.id', '=', 'bom_header.costing_id')
  ->join('style_creation', 'style_creation.style_id', '=', 'costing.style_id')
  ->join('merc_bom_stage', 'merc_bom_stage.bom_stage_id', '=', 'costing.bom_stage_id')
  ->join('org_season', 'org_season.season_id', '=', 'costing.season_id')
  ->join('merc_color_options', 'merc_color_options.col_opt_id', '=', 'costing.color_type_id')
  ->join('item_master', 'item_master.master_id', '=', 'bom_header.fng_id')
  ->where('bom_header.bom_id'  , 'like', $search.'%' )
  ->orWhere('bom_header.sc_no'  , 'like', $search.'%' )
  ->orWhere('style_creation.style_no'  , 'like', $search.'%' )
  ->orWhere('merc_bom_stage.bom_stage_description','like',$search.'%')
  ->orWhere('org_season.season_name','like',$search.'%')
  ->orWhere('merc_color_options.color_option','like',$search.'%')
  ->count();

  echo json_encode([
      "draw" => $draw,
      "recordsTotal" => $bom_count,
      "recordsFiltered" => $bom_count,
      "data" => $bom_list
  ]);
}


}
