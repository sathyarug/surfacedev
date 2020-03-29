<?php

namespace App\Http\Controllers\Merchandising\Costing;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use App\Libraries\AppAuthorize;

use App\Models\Merchandising\Costing\Costing;
use App\Models\Merchandising\Costing\CostingFinishGood;
use App\Models\Merchandising\Costing\CostingFinishGoodComponent;
use App\Models\Merchandising\Costing\CostingFinishGoodComponentItem;
use App\Models\Merchandising\Costing\CostingSalesOrderDelivery;
use App\Models\Merchandising\CustomerOrderDetails;
use App\Models\Merchandising\BOMHeader;
use App\Models\Merchandising\BOMDetails;

class CostingSalesOrderDeliveryController extends Controller
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

       /*if($type == 'fg_items'){
         $fg_component_id = $request->fg_component_id;
         return response([
           'data' => $this->get_items($fg_component_id)
        ]);
      }*/
      $costing_id = $request->costing_id;
      $bom_stage_id = $request->bom_stage_id;
      $style_id = $request->style_id;
      $season_id = $request->season_id;
      $color_type_id = $request->color_type_id;
      $fg_id = $request->fg_id;
      $combo_color = null;//$request->combo_color;

      if($fg_id == null || $fg_id == 0 || $fg_id == false) { //load deliveries for all finish goods
        return response([
          'data' => $this->list($costing_id, null, $style_id, $bom_stage_id, $season_id, $color_type_id)
        ]);
      }
      else { //load only finish good deliveries
        $fg = CostingFinishGood::find($fg_id);
        return response([
          'data' => $this->list($costing_id, $fg->combo_color_id, $style_id, $bom_stage_id, $season_id, $color_type_id)
        ]);
      }
    }


    //create new country
    public function store(Request $request)
    {
        if($request->type == 'AUTO') {
          $costing_id = $request->costing_id;
          $combo_color = null;
          if($request->fg_id != null &&  $request->fg_id != false && $request->fg_id != 0) {
            $fg = CostingFinishGood::find($request->fg_id);
            $combo_color = $fg->combo_color_id;
          }
          return response([
            'data' => $this->auto_connect($costing_id, $combo_color)
          ]);
        }
        else if($request->type == 'MANUAL') {
          $costing_id = $request->costing_id;
          $deliveries = $request->deliveries;
          $fg_id = $request->fg_id;
          return response([
            'data' => $this->manual_connect($costing_id, $deliveries, $fg_id)
          ]);
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
      /*$item_data = $this->generate_item_data($request->item_data);
      //  echo json_encode($item_data);die();
      $fg_item = CostingFinishGoodComponentItem::find($id);
      if($fg_item->validate($item_data))
      {
        $fg_item->fill($item_data);
        $fg_item->save();

        $this->update_finish_good_after_modify_item($fg_item->fg_component_id);

        $saved_item = $this->get_item($fg_item->id);
        $saved_item['edited'] = false;
        return response([
          'data' => [
            'message' => 'Finish good item saved successfully',
            'finish_good_item' => $saved_item
          ]
        ]);
      }
      else{
        $errors = $fg_item->errors();// failure, get errors
        return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
      }*/
    }


    //deactivate a country
    public function destroy($id)
    {
      /*  $item = CostingFinishGoodComponentItem::find($id);
        $item->delete();

        $this->update_finish_good_after_modify_item($item->fg_component_id);

        return response([
          'data' => [
            'message' => 'Item was deleted successfully.',
            'item' => $item,
            'finish_good_items' => $this->get_items($item->fg_component_id)
          ]
        ] , Response::HTTP_OK);*/
    }


    private function list($costing_id, $combo_color, $style_id, $bom_stage_id, $season_id, $color_type_id){
      $query = CustomerOrderDetails::select("merc_customer_order_details.*","org_country.country_description","org_location.loc_name",
        "org_color.color_code","org_color.color_name", DB::raw("(CASE WHEN merc_customer_order_details.delivery_status = 'CONNECTED' THEN 1 ELSE 0 END) AS connected"))
      ->join('org_country', 'org_country.country_id', '=', 'merc_customer_order_details.country')
      ->join('org_location', 'org_location.loc_id', '=', 'merc_customer_order_details.projection_location')
      ->join('org_color', 'org_color.color_id', '=', 'merc_customer_order_details.style_color')
      ->join('merc_customer_order_header', 'merc_customer_order_header.order_id', '=', 'merc_customer_order_details.order_id')
      /*->leftjoin('costing_sales_order_deliveries', function($join) use ($costing_id, $fg_id)
          {
              $join->on('costing_sales_order_deliveries.delivery_id', '=', 'merc_customer_order_details.details_id');
              $join->on('costing_sales_order_deliveries.costing_id', '=', DB::raw("'".$costing_id."'"));
              $join->on('costing_sales_order_deliveries.fg_id', '=', DB::raw("'".$fg_id."'"));
          }
      )*/
      ->where('merc_customer_order_header.order_style', '=', $style_id)
      ->where('merc_customer_order_header.order_stage', '=', $bom_stage_id)
      ->where('merc_customer_order_header.order_season', '=', $season_id)
      ->where('merc_customer_order_details.colour_type', '=', $color_type_id)
      ->where('merc_customer_order_details.delivery_status', '!=', 'CANCELLED')
      ->where('merc_customer_order_details.active_status', '=', 'ACTIVE');

      if($combo_color != null && $combo_color != false && $combo_color != 0) {
        $query->where('merc_customer_order_details.style_color', '=', $combo_color);
      }
      $deliveries = $query->orderBy('merc_customer_order_details.style_color', 'ASC')->get();
      return $deliveries;
    }


    private function manual_connect($costing_id, $deliveries, $fg_id){
      $err_message = null;
      $costing = Costing::find($costing_id);

      for($x = 0; $x < sizeof($deliveries) ; $x++){
        $delivery = CustomerOrderDetails::find($deliveries[$x]['details_id']);

        if($deliveries[$x]['connected'] == 1){ //check delivery connected
          if($delivery->delivery_status == 'PLANNED') { //connect only status == PLANNED. no need to update already connected delivery
            //get finish good which map to combo color
            $fg = CostingFinishGood::where('costing_id', '=', $costing_id)->where('combo_color_id', '=', $deliveries[$x]['style_color'])->first();
            if($fg != null) { //has finish good for combo color
              $delivery->costing_id = $costing_id;
              $delivery->fg_id = $fg->fg_id;
              $delivery->costing_connected_by = 19;
              $delivery->costing_connected_date = date("Y-m-d H:i:s");
              $delivery->delivery_status = 'CONNECTED';
              $delivery->save();

              if($costing->status == 'APPROVED') {
                $this->generate_bom_for_delivery($costing, $delivery);
              }

              /*$bom = new BOMHeader();
              $bom->costing_id = $deliveries[$y]->costing_id;
              $bom->delivery_id = $deliveries[$y]->details_id;
              $bom->sc_no = $costing->sc_no;
              $bom->status = 1;
              $bom->save();*/

              /*$components = CostingFinishGoodComponent::where('fg_id', '=', $deliveries[$y]->fg_id)->get()->pluck('id');
              $items = CostingFinishGoodComponentItem::whereIn('fg_component_id', $components)->get();
              $items = json_decode(json_encode($items), true); //conver to array
              for($x = 0 ; $x < sizeof($items); $x++) {
                $items[$x]['bom_id'] = $bom->bom_id;
                $items[$x]['costing_item_id'] = $items[$x]['id'];
                $items[$x]['id'] = 0; //clear id of previous data, will be auto generated
                $items[$x]['bom_unit_price'] = $items[$x]['unit_price'];
                $items[$x]['order_qty'] = $deliveries[$y]->order_qty * $items[$x]['gross_consumption'];
                $items[$x]['required_qty'] = $deliveries[$y]->order_qty * $items[$x]['gross_consumption'];
                $items[$x]['total_cost'] = (($items[$x]['unit_price'] * $items[$x]['gross_consumption'] * $deliveries[$y]->order_qty) + $items[$x]['freight_charges'] + $items[$x]['surcharge']);
                $items[$x]['created_date'] = null;
                $items[$x]['created_by'] = null;
                $items[$x]['updated_date'] = null;
                $items[$x]['updated_by'] = null;
              }
              DB::table('bom_details')->insert($items);*/
            }
            else {//no finish good, show error
              $err_message = 'Cannot save delivery '.$deliveries[$x]['line_no']. '. There is no finish good for combo color '. $deliveries[$x]['color_name'];
              break;
            }
          }
        }
        else {//not connected
          if($delivery->delivery_status == 'CONNECTED'){ //remove the connection and set data to PLANNED status
            //delete bom and bom items
            $bom = BOMHeader::where('delivery_id', '=', $delivery->details_id)->first();
            if($bom != null) {
              $bom_details = BOMDetails::where('bom_id', '=', $bom->bom_id)->get();
              $can_remove_count = 0;
              $can_deactivate_count = 0;

              foreach($bom_details as $row){
                if($row->po_con == null){
                  $can_remove_count++;
                  $can_deactivate_count++;
                }
                else if($row->po_con == 'CREATE'){ //has active po line
                  $can_remove_count--;
                  $can_deactivate_count--;
                  break;
                }
                else if($row->po_con == 'CANCELLED'){
                    $can_remove_count--;
                    $can_deactivate_count++;
                }
              }

              $can_disconnect = false;
              if($can_remove_count >= sizeof($bom_details)) {//no active po lines for bom
                BOMDetails::where('bom_id', '=', $bom->bom_id)->delete();
                $bom->delete();
                $can_disconnect = true;
              }
              else if($can_deactivate_count >= sizeof($bom_details)){
                BOMDetails::where('bom_id', '=', $bom->bom_id)->update(['status'=> 0]);
                $bom->status = 0;
                $bom->save();
                $can_disconnect = true;
              }

              if($can_disconnect == true) {
                $delivery->costing_id = null;
                $delivery->fg_id = null;
                $delivery->costing_connected_by = null;
                $delivery->costing_connected_date = null;
                $delivery->delivery_status = 'PLANNED';
                $delivery->save();
              }
            }
            else { // no bom, so connectins can be removed
              $delivery->costing_id = null;
              $delivery->fg_id = null;
              $delivery->costing_connected_by = null;
              $delivery->costing_connected_date = null;
              $delivery->delivery_status = 'PLANNED';
              $delivery->save();
            }
          }
        }
      }

      $combo_color = null;
      if($fg_id != null && $fg_id != false && $fg_id != 0){ // only load single finish good deliveries
        $fg = CostingFinishGood::find($fg_id);
        $combo_color = $fg->combo_color_id;
      }
      $updated_deliveries = $this->list($costing_id, $combo_color, $costing->style_id, $costing->bom_stage_id, $costing->season_id, $costing->color_type_id);

      if($err_message == null) {
        return [
            'status' => 'success',
            'message' => 'Sales order deliveries connected successfully.',
            'deliveries' => $updated_deliveries
        ];
      }
      else{
        return [
          'status' => 'error',
          'message' => $err_message,
          'deliveries' => $updated_deliveries
        ];
      }
    }


  private function auto_connect($costing_id, $combo_color /* will be null if auto connect for whole costing*/){
      //$err_message = null;
      $costing = Costing::find($costing_id);
      $deliveries = [];
      $deliveries = $this->list($costing_id, $combo_color, $costing->style_id, $costing->bom_stage_id, $costing->season_id, $costing->color_type_id);
      $date = date("Y-m-d H:i:s");

      if(sizeof($deliveries) > 0) {
        for($x = 0; $x < sizeof($deliveries) ; $x++){
          $delivery = CustomerOrderDetails::find($deliveries[$x]['details_id']);

            if($delivery->delivery_status == 'PLANNED') { //connect only status == PLANNED. no need to update already connected delivery
              //get finish good which map to combo color
              $fg = CostingFinishGood::where('costing_id', '=', $costing_id)->where('combo_color_id', '=', $deliveries[$x]['style_color'])->first();
              if($fg != null) { //has finish good for combo color
                $delivery->costing_id = $costing_id;
                $delivery->fg_id = $fg->fg_id;
                $delivery->costing_connected_by = 19;
                $delivery->costing_connected_date = $date;
                $delivery->delivery_status = 'CONNECTED';
                $delivery->save();

                if($costing->status == 'APPROVED') {
                  $this->generate_bom_for_delivery($costing, $delivery);//generate bom for delivery
                }
              }
            }
        }

        return [
          'status' => 'success',
          'message' => 'Sales order deliveries connected successfully.',
          'deliveries' => []
        ];
      }
      else {
        return [
          'status' => 'error',
          'message' => 'No sales order deliveries to connect.',
          'deliveries' => []
        ];
      }
    }




    private function generate_bom_for_delivery($costing, $delivery) {
      //create bom
      $bom = new BOMHeader();
      $bom->costing_id = $delivery->costing_id;
      $bom->delivery_id = $delivery->details_id;
      $bom->sc_no = $costing->sc_no;
      $bom->status = 1;
      $bom->save();

      $components = CostingFinishGoodComponent::where('fg_id', '=', $delivery->fg_id)->get()->pluck('id');
      $items = CostingFinishGoodComponentItem::whereIn('fg_component_id', $components)->get();
      $items = json_decode(json_encode($items), true); //conver to array
      for($x = 0 ; $x < sizeof($items); $x++) {
        $items[$x]['bom_id'] = $bom->bom_id;
        $items[$x]['costing_item_id'] = $items[$x]['id'];
        $items[$x]['id'] = 0; //clear id of previous data, will be auto generated
        $items[$x]['bom_unit_price'] = $items[$x]['unit_price'];
        $items[$x]['order_qty'] = $delivery->order_qty * $items[$x]['gross_consumption'];
        $items[$x]['required_qty'] = $delivery->order_qty * $items[$x]['gross_consumption'];
        $items[$x]['total_cost'] = (($items[$x]['unit_price'] * $items[$x]['gross_consumption'] * $delivery->order_qty) + $items[$x]['freight_charges'] + $items[$x]['surcharge']);
        $items[$x]['created_date'] = null;
        $items[$x]['created_by'] = null;
        $items[$x]['updated_date'] = null;
        $items[$x]['updated_by'] = null;
      }
      DB::table('bom_details')->insert($items);

    }

}
