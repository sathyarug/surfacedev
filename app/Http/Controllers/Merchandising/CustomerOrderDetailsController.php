<?php

namespace App\Http\Controllers\Merchandising;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use App\Models\Merchandising\CustomerOrderDetails;
use App\Models\Merchandising\CustomerOrder;
use App\Models\Merchandising\CustomerOrderSize;
//use App\Libraries\UniqueIdGenerator;
use App\Models\Merchandising\StyleCreation;
use App\Libraries\CapitalizeAllFields;

use App\Models\Merchandising\BOMHeader;
use App\Models\Merchandising\BOMDetails;
use App\Models\Merchandising\Costing\Costing;
use App\Models\Merchandising\Costing\CostingFinishGood;
use App\Models\Merchandising\Costing\CostingFinishGoodComponent;
use App\Models\Merchandising\Costing\CostingFinishGoodComponentItem;

use App\Models\Merchandising\ShopOrderHeader;
use App\Models\Merchandising\ShopOrderDetail;
use App\Models\Merchandising\ShopOrderDelivery;
use App\Models\Merchandising\Item\Item;

class CustomerOrderDetailsController extends Controller
{
    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => ['index','show']]);
    }

    //get customer list
    public function index(Request $request)
    {
      //$id_generator = new UniqueIdGenerator();
      //echo $id_generator->generateCustomerOrderDetailsId('CUSTOMER_ORDER' , 1);
      //echo UniqueIdGenerator::generateUniqueId('CUSTOMER_ORDER' , 2 , 'FDN');
      $type = $request->type;
      if($type == 'datatable') {
        $data = $request->all();
        return response($this->datatable_search($data));
      }
      else if($type == 'auto')  {
        $search = $request->search;
        return response($this->autocomplete_search($search));
      }
      else if($type == 'style_colors')  {
        $style = $request->style;
        return response(['data' => $this->style_colors($style)]);
      }else if($type == 'customer_po_for_so')  {
          $cusOrder = $request->order_id;
          $fields = $request->fields;
          return response([
              'data' => $this->getCustomerPoForSO($cusOrder, $fields)
          ]);
      }
      else if($type=='auto_detail_id'){
        $search = $request->search;
        return response($this->autocomplete_detail_id_search($search));
      }
      else{
        $order_id = $request->order_id;
        return response(['data' => $this->list($order_id)]);
      }
    }


    //create a customer
    public function store(Request $request)
    {
      $order_details = new CustomerOrderDetails();
      if($order_details->validate($request->all()))
      {

        $check_duplicate = CustomerOrderDetails::select('*')
           ->where('style_color' , '=', $request->style_color )
           ->where('pcd' , '=', $request->pcd )
           ->where('rm_in_date' , '=', $request->rm_in_date )
           ->where('po_no' , '=', $request->po_no )
           ->where('planned_delivery_date' , '=', $request->planned_delivery_date )
           ->where('fob' , '=', $request->fob )
           ->where('country' , '=', $request->country )
           ->where('projection_location' , '=', $request->projection_location )
           ->where('order_qty' , '=', $request->order_qty )
           ->where('excess_presentage' , '=', $request->excess_presentage )
           ->where('ship_mode' , '=', $request->ship_mode )
           ->where('ex_factory_date' , '=', $request->ex_factory_date )
           ->where('colour_type' , '=', $request->colour_type )
           ->where('ac_date' , '=', $request->ac_date )
           ->where('cus_style_manual' , '=', $request->cus_style_manual )
           ->where('order_id' , '=', $request->order_id )
           ->where('delivery_status' , '<>', 'CANCELLED' )
           ->get();

           //dd(sizeof($check_duplicate));
           //dd($request);

           if(sizeof($check_duplicate) != 0){
             return response([ 'data' => ['status' => 'error','message' => 'Order Line Details already exist ..!']]);
           }

        $order_details->fill($request->all());
        $capitalizeAllFields=CapitalizeAllFields::setCapitalAll($order_details);
        $order_details->style_description = '';
        $order_details->delivery_status = 'PLANNED';
        $order_details->version_no = 0;
        $order_details->line_no = $this->get_next_line_no($order_details->order_id);
        $order_details->type_created = 'CREATE';
        $order_details->active_status = 'ACTIVE';
        $order_details->save();
        //$order_details = CustomerOrderDetails::with(['order_country','order_location'])->find($order_details->details_id);
        $order_details = $this->get_delivery_details($order_details->details_id);

        return response([ 'data' => [
          'message' => 'Sales order line saved successfully',
          'customerOrderDetails' => $order_details
          ]
        ], Response::HTTP_CREATED );


      }
      else
      {
          $errors = $order_details->errors();// failure, get errors
          return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
      }
    }


    //get a customer
    public function show($id)
    {
      $detail = CustomerOrderDetails::with(['order_country','order_location'])->find($id);
      $header = CustomerOrder::find($detail['order_id']);
      $fng = $detail['fng_id'];

      //dd($fng);

      $colour_type = CustomerOrderDetails::select('merc_color_options.col_opt_id', 'merc_color_options.color_option')
                   ->join('merc_color_options', 'merc_customer_order_details.colour_type', '=', 'merc_color_options.col_opt_id')
                   ->where('merc_customer_order_details.details_id', '=', $id)
                   ->get();

      $detail['col_type'] = $colour_type;

    /*  $st_colour = Costing::select('item_master.master_id','item_master.master_code','item_master.master_description','org_color.color_id', 'org_color.color_code')
                   ->join('bom_header', 'costing.id', '=', 'bom_header.costing_id')
                   ->join('item_master', 'bom_header.fng_id', '=', 'item_master.master_id')
                   ->join('org_color', 'item_master.color_id', '=', 'org_color.color_id')
                   ->where('style_id', '=', $header['order_style'])
                   ->where('bom_stage_id', '=', $header['order_stage'])
                   ->where('season_id', '=', $header['order_season'])
                   ->where('color_type_id', '=', $detail['colour_type'])
                   ->get(); */

       $fng_main=  DB::table('item_master')
                     ->select('item_master.master_id','item_master.master_code')
                     ->where('item_master.master_id' , '=', $fng )
                     ->get();

       $fng_col=  DB::table('item_master')
                     ->select('org_color.color_id', 'org_color.color_code')
                     ->join('org_color', 'item_master.color_id', '=', 'org_color.color_id')
                     ->where('item_master.master_id' , '=', $fng )
                     ->get();

       $item_des =  DB::table('item_master')
                     ->select('item_master.master_description')
                     ->where('item_master.master_id' , '=', $fng )
                     ->get();

       $fng_fob =  DB::table('bom_header')
                     ->select('bom_header.fob')
                     ->where('bom_header.fng_id' , '=', $fng )
                     ->get();

      $detail['style_fng']  = $fng_main;
      $detail['style_colour']  = $fng_col;
      $detail['item_des']  = $item_des;
      $detail['fob']  = $fng_fob;


      $fng_country_lists =  DB::table('bom_header')
                      ->select('org_country.country_id','org_country.country_description')
                      ->join('org_country', 'bom_header.country_id', '=', 'org_country.country_id')
                      ->where('bom_header.fng_id' , '=', $fng )
                      ->get();
      $detail['country'] = $fng_country_lists;

    /*  $st_colour = Item::select('org_color.color_id', 'org_color.color_code')
                   ->join('org_color', 'item_master.color_id', '=', 'org_color.color_id')
                   ->where('item_master.master_id', '=', $fng)
                   ->get();
      $arr['item_fng_colour']  = $st_colour;

      $fng_fob =  DB::table('bom_header')
                      ->select('bom_header.fob')
                      ->where('bom_header.fng_id' , '=', $fng )
                      ->get();

      $item_des =  DB::table('item_master')
                      ->select('item_master.master_description')
                      ->where('item_master.master_id' , '=', $fng )
                      ->get();*/

      if($detail == null)
        throw new ModelNotFoundException("Requested order details not found", 1);
      else
        return response([ 'data' => $detail ]);
    }


    //update a customer
    public function update(Request $request, $id)
    {
        $order_details = CustomerOrderDetails::find($id);
        $message = '';
      //    echo json_encode($order_details);die();
        if($order_details->validate($request->all()))
        {
          $check_duplicate = CustomerOrderDetails::select('*')
             ->where('style_color' , '=', $request->style_color )
             ->where('pcd' , '=', $request->pcd )
             ->where('rm_in_date' , '=', $request->rm_in_date )
             ->where('po_no' , '=', $request->po_no )
             ->where('planned_delivery_date' , '=', $request->planned_delivery_date )
             ->where('fob' , '=', $request->fob )
             ->where('country' , '=', $request->country )
             ->where('projection_location' , '=', $request->projection_location )
             ->where('order_qty' , '=', $request->order_qty )
             ->where('excess_presentage' , '=', $request->excess_presentage )
             ->where('ship_mode' , '=', $request->ship_mode )
             ->where('ex_factory_date' , '=', $request->ex_factory_date )
             ->where('colour_type' , '=', $request->colour_type )
             ->where('ac_date' , '=', $request->ac_date )
             ->where('cus_style_manual' , '=', $request->cus_style_manual )
             ->where('order_id' , '=', $request->order_id )
             ->where('delivery_status' , '<>', 'CANCELLED' )
             ->get();

             //dd(sizeof($check_duplicate));
             //dd($request);

             if(sizeof($check_duplicate) != 0){
               return response([ 'data' => ['status' => 'error','message' => 'Order Line Details already exist ..!']]);
             }

          $order_details_new = new CustomerOrderDetails();
          $order_details_new->fill($request->all());
          $order_details_new->delivery_status = $order_details->delivery_status;
          $order_details_new->version_no = $order_details->version_no + 1;
          $order_details_new->line_no = $order_details->line_no;
          $order_details_new->type_created = $order_details->type_created;
          $order_details_new->type_modified = $order_details->type_modified;
          $order_details_new->parent_line_no = $order_details->parent_line_no;
          $order_details_new->parent_line_id = $order_details->parent_line_id;
          $order_details_new->split_lines = $order_details->split_lines;
          $order_details_new->merged_line_nos = $order_details->merged_line_nos;
          $order_details_new->merged_line_ids = $order_details->merged_line_ids;
          $order_details_new->merge_generated_line_id = $order_details->merge_generated_line_id;
          $order_details_new->shop_order_id = $order_details->shop_order_id;
          $order_details_new->shop_order_connected_by = $order_details->shop_order_connected_by;
          $order_details_new->shop_order_connected_date = $order_details->shop_order_connected_date;
          $order_details_new->active_status = 'ACTIVE';
          $order_details_new->save();

          DB::table('merc_customer_order_details')
              ->where('details_id', $id)
              ->update(['active_status' => 'INACTIVE']);



          $balance = $order_details_new->planned_qty - $order_details->planned_qty;
          if($order_details_new->order_qty == $order_details->order_qty){
              $sizes = CustomerOrderSize::where('details_id','=',$order_details->details_id)->get();
              foreach($sizes as $size){
                $new_size = new CustomerOrderSize();
                $new_size->details_id = $order_details_new->details_id;
                $new_size->size_id = $size->size_id;
                $new_size->order_qty = $size->order_qty;
                $new_size->excess_presentage = $size->excess_presentage;
                $new_size->planned_qty = $size->planned_qty;
                $new_size->version_no = $size->version_no;
                $new_size->line_no = $size->line_no;
                $new_size->save();
              }
              $message = 'Sales order line updated successfully';
          }
          else{
              $message = 'Sales order line updated successfully. But planned qty mismatch. Please enter size qty again.';
          }
          //$order_details_new = CustomerOrderDetails::with(['order_country','order_location'])->find($order_details_new->details_id);
          $order_details_new = $this->get_delivery_details($order_details_new->details_id);

          return response([ 'data' => [
            'message' => $message,
            'customerOrderDetails' => $order_details_new
            ]
          ], Response::HTTP_CREATED );
        }
        else
        {
            $errors = $order_details->errors();// failure, get errors
            return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }


    //deactivate a customer
    public function destroy($id)
    {
      /*$customer = Customer::where('customer_id', $id)->update(['status' => 0]);*/
      return response([
        'data' => [
          'message' => 'Sales deactivated successfully.',
          'customer' => null
        ]
      ] , Response::HTTP_NO_CONTENT);
    }

    public function copy_line(Request $request){

      $check = $request->check;
      $line_id = $request->line_id;

      $order_details = CustomerOrderDetails::find($line_id);

        $order_details_new = new CustomerOrderDetails();
        $order_details_new->order_id = $order_details->order_id;
        $order_details_new->fng_id = $order_details->fng_id;
        $order_details_new->style_color = $order_details->style_color;
        $order_details_new->colour_type = $order_details->colour_type;
        $order_details_new->style_description = $order_details->style_description;
        $order_details_new->pcd = $order_details->pcd;
        $order_details_new->rm_in_date = $order_details->rm_in_date;
        $order_details_new->po_no = $order_details->po_no;
        $order_details_new->planned_delivery_date = $order_details->planned_delivery_date;
        $order_details_new->fob = $order_details->fob;
        $order_details_new->country = $order_details->country;
        $order_details_new->projection_location = $order_details->projection_location;
        $order_details_new->order_qty = $order_details->order_qty;
        $order_details_new->excess_presentage = $order_details->excess_presentage;
        $order_details_new->planned_qty = $order_details->planned_qty;
        $order_details_new->ship_mode = $order_details->ship_mode;
        $order_details_new->delivery_status = 'PLANNED';
        $order_details_new->type_created = 'CREATE';
        $order_details_new->ex_factory_date = $order_details->ex_factory_date;
        $order_details_new->ac_date = $order_details->ac_date;
        $order_details_new->version_no = 0;
        $order_details_new->line_no = $this->get_next_line_no($order_details->order_id);
        $order_details_new->active_status = 'ACTIVE';
        $order_details_new->save();

        if($check == 1){

          $sizes = CustomerOrderSize::where('details_id','=',$line_id)->get();
          //echo $sizes ;
          for($y = 0 ; $y < sizeof($sizes) ; $y++){

            $new_size = new CustomerOrderSize();
            $new_size->details_id = $order_details_new->details_id;
            $new_size->size_id = $sizes[$y]['size_id'];
            $new_size->order_qty = $sizes[$y]['order_qty'];
            $new_size->excess_presentage = $sizes[$y]['excess_presentage'];
            $new_size->planned_qty = $sizes[$y]['planned_qty'];
            $new_size->version_no = $sizes[$y]['version_no'];
            $new_size->line_no = $sizes[$y]['line_no'];
            $new_size->status = $sizes[$y]['status'];
            $new_size->save();


          }




        }

        return response([
          'data' => [
            'status' => 'success',
            'message' => 'Successfully Copied.'
          ]
        ] , 200);


    }


    public function delete_line(Request $request){

      $details_id   = $request->details_id;
      $status       = $request->status;
      $order_id     = $request->order_id;

      if($status == 'PLANNED'){
        $line = CustomerOrderDetails::find($details_id);
        if($line['delivery_status'] == 'PLANNED'){
          $updateDetails = ['active_status' => 'INACTIVE','delivery_status' => 'CANCELLED'];
          CustomerOrderDetails::where('details_id', $details_id)->update($updateDetails);
        }else{
          return response([
            'data' => [
              'status' => 'error',
              'message' => "This line alredy released ."
            ]
          ] , 200);
        }


      }
      else if ($status == 'CANCELLED'){

        CustomerOrderDetails::where('details_id', $details_id)
            ->update(['active_status' => 'INACTIVE']);

      }

      return response([
        'data' => [
          'status' => 'success',
          'message' => 'Successfully Deleted.'
        ]
      ] , 200);


  }


  public function released_SO_All(Request $request){

    $details  = $request->details;
    //dd($details);
    $shop_order_id = '';
    for($x = 0 ; $x < sizeof($details) ; $x++)
    {
      if($details[$x]['delivery_status'] == 'PLANNED')
      {
        $shoporder = new ShopOrderHeader();
        $shoporder->order_qty = $request->details[$x]['order_qty'];
        $shoporder->fg_id = $request->details[$x]['fng_id'];
        $shoporder->order_status = 'RELEASED';
        $shoporder->status = '1';
        $shoporder->save();

        $shop_order_id = $shoporder->shop_order_id;

        $shoporder_delivery = new ShopOrderDelivery();
        $shoporder_delivery->shop_order_id = $shop_order_id;
        $shoporder_delivery->delivery_id = $request->details[$x]['details_id'];
        $shoporder_delivery->status = '1';
        $shoporder_delivery->save();

        $load_Bom_details = BOMHeader::join('bom_details', 'bom_details.bom_id', '=', 'bom_header.bom_id')
         ->select('bom_details.*','bom_header.fng_id')
         ->Where('bom_header.fng_id','=', $request->details[$x]['fng_id'])
         ->get();

         for($y = 0 ; $y < sizeof($load_Bom_details) ; $y++)
         {

         $shoporder_detail= new ShopOrderDetail();
         $shoporder_detail->shop_order_id = $shop_order_id;
         $shoporder_detail->bom_id = $load_Bom_details[$y]['bom_id'];
         $shoporder_detail->bom_detail_id = $load_Bom_details[$y]['bom_detail_id'];
         $shoporder_detail->costing_item_id = $load_Bom_details[$y]['costing_item_id'];
         $shoporder_detail->costing_id = $load_Bom_details[$y]['costing_id'];
         $shoporder_detail->component_id = $load_Bom_details[$y]['product_component_id'];
         $shoporder_detail->inventory_part_id = $load_Bom_details[$y]['inventory_part_id'];
         $shoporder_detail->supplier = $load_Bom_details[$y]['supplier_id'];
         $shoporder_detail->purchase_price = $load_Bom_details[$y]['purchase_price'];
         $shoporder_detail->postion_id = $load_Bom_details[$y]['position_id'];
         $shoporder_detail->purchase_uom = $load_Bom_details[$y]['purchase_uom_id'];
         $shoporder_detail->orign_type_id = $load_Bom_details[$y]['origin_type_id'];
         $shoporder_detail->garment_option_id = $load_Bom_details[$y]['garment_options_id'];
         $shoporder_detail->unit_price = $load_Bom_details[$y]['bom_unit_price'];
         $shoporder_detail->net_consumption = $load_Bom_details[$y]['net_consumption'];
         $shoporder_detail->wastage = $load_Bom_details[$y]['wastage'];
         $shoporder_detail->gross_consumption = $load_Bom_details[$y]['gross_consumption'];
         $shoporder_detail->material_type = $load_Bom_details[$y]['meterial_type'];
         $shoporder_detail->freight_charges = $load_Bom_details[$y]['freight_charges'];
         $shoporder_detail->surcharge = $load_Bom_details[$y]['surcharge'];
         $shoporder_detail->total_cost = $load_Bom_details[$y]['total_cost'];
         $shoporder_detail->ship_mode = $load_Bom_details[$y]['ship_mode'];
         $shoporder_detail->ship_term = $load_Bom_details[$y]['ship_term'];
         $shoporder_detail->lead_time = $load_Bom_details[$y]['lead_time'];
         $shoporder_detail->country_id = $load_Bom_details[$y]['country_id'];
         $shoporder_detail->comments = $load_Bom_details[$y]['comments'];

         $shoporder_detail->status = '1';
         $shoporder_detail->save();


         }

         $user = auth()->user();

         DB::table('merc_customer_order_details')
           ->where('details_id', $request->details[$x]['details_id'])
           ->update(['shop_order_id' => $shop_order_id,
                     'shop_order_connected_by' => $user->user_id,
                     'shop_order_connected_date' => date("Y-m-d H:i:s"),
                     'delivery_status' => 'RELEASED']);

         DB::table('merc_customer_order_header')
             ->where('order_id', $request->details['order_id'])
             ->update(['order_status' => 'RELEASED']);

      }else{

        //return response([ 'data' => ['status' => 'error','message' => 'already released !']]);
      }
    }

    return response([
      'data' => [
        'status' => 'success',
        'message' => 'Successfully Released.'
      ]
    ] , 200);

  }

  public function released_SO(Request $request){

    $check_shop_order = CustomerOrderDetails::where([['shop_order_id', '!=', null],['details_id','=',$request->details['details_id']]])->first();
    if($check_shop_order != null)
    {

      return response([
        'data' => [
          'status' => 'error',
          'message' => 'Already Released.'
        ]
      ] , 200);


    }else{


      $shoporder = new ShopOrderHeader();
      $shoporder->order_qty = $request->details['order_qty'];
      $shoporder->fg_id = $request->details['fng_id'];
      $shoporder->order_status = 'RELEASED';
      $shoporder->status = '1';
      $shoporder->save();

      $shop_order_id = $shoporder->shop_order_id;

      $shoporder_delivery = new ShopOrderDelivery();
      $shoporder_delivery->shop_order_id = $shop_order_id;
      $shoporder_delivery->delivery_id = $request->details['details_id'];
      $shoporder_delivery->status = '1';
      $shoporder_delivery->save();

      $load_Bom_details = BOMHeader::join('bom_details', 'bom_details.bom_id', '=', 'bom_header.bom_id')
       ->select('bom_details.*','bom_header.fng_id')
       ->Where('bom_header.fng_id','=', $request->details['fng_id'])
       ->get();

      for($x = 0 ; $x < sizeof($load_Bom_details) ; $x++)
      {

      $shoporder_detail= new ShopOrderDetail();
      $shoporder_detail->shop_order_id = $shop_order_id;
      $shoporder_detail->bom_id = $load_Bom_details[$x]['bom_id'];
      $shoporder_detail->bom_detail_id = $load_Bom_details[$x]['bom_detail_id'];
      $shoporder_detail->costing_item_id = $load_Bom_details[$x]['costing_item_id'];
      $shoporder_detail->costing_id = $load_Bom_details[$x]['costing_id'];
      $shoporder_detail->component_id = $load_Bom_details[$x]['product_component_id'];
      $shoporder_detail->inventory_part_id = $load_Bom_details[$x]['inventory_part_id'];
      $shoporder_detail->supplier = $load_Bom_details[$x]['supplier_id'];
      $shoporder_detail->purchase_price = $load_Bom_details[$x]['purchase_price'];
      $shoporder_detail->postion_id = $load_Bom_details[$x]['position_id'];
      $shoporder_detail->purchase_uom = $load_Bom_details[$x]['purchase_uom_id'];
      $shoporder_detail->orign_type_id = $load_Bom_details[$x]['origin_type_id'];
      $shoporder_detail->garment_option_id = $load_Bom_details[$x]['garment_options_id'];
      $shoporder_detail->unit_price = $load_Bom_details[$x]['bom_unit_price'];
      $shoporder_detail->net_consumption = $load_Bom_details[$x]['net_consumption'];
      $shoporder_detail->wastage = $load_Bom_details[$x]['wastage'];
      $shoporder_detail->gross_consumption = $load_Bom_details[$x]['gross_consumption'];
      $shoporder_detail->material_type = $load_Bom_details[$x]['meterial_type'];
      $shoporder_detail->freight_charges = $load_Bom_details[$x]['freight_charges'];
      $shoporder_detail->surcharge = $load_Bom_details[$x]['surcharge'];
      $shoporder_detail->total_cost = $load_Bom_details[$x]['total_cost'];
      $shoporder_detail->ship_mode = $load_Bom_details[$x]['ship_mode'];
      $shoporder_detail->ship_term = $load_Bom_details[$x]['ship_term'];
      $shoporder_detail->lead_time = $load_Bom_details[$x]['lead_time'];
      $shoporder_detail->country_id = $load_Bom_details[$x]['country_id'];
      $shoporder_detail->comments = $load_Bom_details[$x]['comments'];

      $shoporder_detail->status = '1';
      $shoporder_detail->save();


      }

      $user = auth()->user();

      DB::table('merc_customer_order_details')
        ->where('details_id', $request->details['details_id'])
        ->update(['shop_order_id' => $shop_order_id,
                  'shop_order_connected_by' => $user->user_id,
                  'shop_order_connected_date' => date("Y-m-d H:i:s"),
                  'delivery_status' => 'RELEASED']);

       DB::table('merc_customer_order_header')
         ->where('order_id', $request->details['order_id'])
         ->update(['order_status' => 'RELEASED']);


      return response([
        'data' => [
          'status' => 'success',
          'message' => 'Successfully Released.'
        ]
      ] , 200);
    }



}


    //validate anything based on requirements
    public function validate_data(Request $request){
      /*$for = $request->for;
      if($for == 'duplicate')
      {
        return response($this->validate_duplicate_code($request->customer_id , $request->customer_code));
      }*/
    }


    public function customer_divisions(Request $request) {
        /*$type = $request->type;
        $customer_id = $request->customer_id;

        if($type == 'selected')
        {
          $selected = Division::select('division_id','division_description')
          ->whereIn('division_id' , function($selected) use ($customer_id){
              $selected->select('division_id')
              ->from('org_customer_divisions')
              ->where('customer_id', $customer_id);
          })->get();
          return response([ 'data' => $selected]);
        }
        else
        {
          $notSelected = Division::select('division_id','division_description')
          ->whereNotIn('division_id' , function($notSelected) use ($customer_id){
              $notSelected->select('division_id')
              ->from('org_customer_divisions')
              ->where('customer_id', $customer_id);
          })->get();
          return response([ 'data' => $notSelected]);
        }*/

    }

    public function getCustomerPoForSO($cusOrder, $fields){
        $fields = explode(',', $fields);

        $customerOrder = CustomerOrderDetails::select($fields);
        $customerOrder->where('order_id','=',$cusOrder);
        return $customerOrder->get();
    }

    public function split_delivery(Request $request)
    {
      $split_count = $request->split_count;
      $delivery_id = $request->delivery_id;
      $lines       = $request->lines;

      $delivery = CustomerOrderDetails::find($delivery_id);
      $excess_presentage = $delivery->excess_presentage;

      //$split_order_qty = ceil($delivery->order_qty / $split_count);
      // $split_plan_qty = ceil((($split_order_qty * $delivery->excess_presentage) / 100) + $split_order_qty);

      $sizes = CustomerOrderSize::where('details_id','=',$delivery->details_id)->get();//get all sizes belongs to current delivery
      $new_delivery_ids = [];

      for($x = 0 ; $x < $split_count ; $x++) //create new lines
      {
        $delivery_new = new CustomerOrderDetails();
        $delivery_new->order_id = $delivery['order_id'];
        $delivery_new->style_color = $delivery['style_color'];
        $delivery_new->pcd = $delivery['pcd'];
        $delivery_new->rm_in_date = $delivery['rm_in_date'];
        $delivery_new->po_no = $delivery['po_no'];
        $delivery_new->fng_id = $delivery['fng_id'];
        $delivery_new->planned_delivery_date = $delivery['planned_delivery_date'];
        $delivery_new->projection_location = $delivery['projection_location'];
        $delivery_new->fob = $delivery['fob'];
        $delivery_new->country = $delivery['country'];
        $delivery_new->excess_presentage = $delivery['excess_presentage'];
        $delivery_new->ship_mode = $delivery['ship_mode'];
        $delivery_new->delivery_status = $delivery['delivery_status'];
        $delivery_new->order_qty = $lines[$x]['order_qty'];
        $delivery_new->planned_qty = $lines[$x]['planned_qty'];
        $delivery_new->line_no = $this->get_next_line_no($delivery->order_id);
        $delivery_new->version_no = 0;
        $delivery_new->parent_line_id = $delivery->details_id;
        $delivery_new->parent_line_no = $delivery->line_no;
        $delivery_new->type_created = 'GFS';
        $delivery_new->ex_factory_date = $delivery['ex_factory_date'];
        $delivery_new->ac_date = $delivery['ac_date'];
        $delivery_new->active_status = 'ACTIVE';
        $delivery_new->costing_id = $delivery['costing_id'];
        $delivery_new->fg_id = $delivery['fg_id'];
        $delivery_new->colour_type = $delivery['colour_type'];
        $delivery_new->cus_style_manual = $delivery['cus_style_manual'];
        $delivery_new->save();

          array_push($new_delivery_ids , $delivery_new->details_id);
        /*  $split_order_qty = 0;
          $split_plan_qty = 0;

        foreach($sizes as $size){ //create new sizes with new order qty
          $order_qty2 = $size->order_qty;
          $split_order_qty2 = ceil($order_qty2 / $split_count);
          $split_planned_qty2 = ceil((($split_order_qty2 * $excess_presentage) / 100) + $split_order_qty2);

          $split_order_qty += $split_order_qty2;
          $split_plan_qty += $split_planned_qty2;

          $new_size = new CustomerOrderSize();
          $new_size->details_id = $delivery_new->details_id;
          $new_size->size_id = $size->size_id;
          $new_size->order_qty = $split_order_qty2;
          $new_size->excess_presentage = $excess_presentage;
          $new_size->planned_qty = $split_planned_qty2;
          $new_size->version_no = 0;
          $new_size->line_no = 1;
          $new_size->save();
        }

        if(sizeof($sizes) > 0){
          $delivery_new->order_qty = $split_order_qty;//update order qty and planned qty
          $delivery_new->planned_qty = $split_plan_qty;
        }
        else{ //no sizes avalibale and split main order qty
          $s_qty = ceil($delivery['order_qty'] / $split_count);
          $delivery_new->order_qty = $s_qty;
          $delivery_new->planned_qty = ceil((($s_qty * $excess_presentage) / 100) + $s_qty);
        } */

        $delivery_new->save();

        //generate new bom for delivery
        /*$_costing = Costing::find($delivery_new->costing_id);
        if($_costing->status == 'APPROVED' && $delivery_new->delivery_status == 'CONNECTED'){
          $this->generate_bom_for_delivery($_costing, $delivery_new);
        }*/

      }

      $new_delivery_ids_str = json_encode($new_delivery_ids);
      $delivery->split_lines = $new_delivery_ids_str;
      $delivery->delivery_status = 'CANCELLED';
      $delivery->type_modified = 'SPLIT';
      $delivery->active_status = 'INACTIVE';
      $delivery->save();

      //if bom exists deactivate bom and all bom items
      /*$bom = BOMHeader::where('delivery_id', '=', $delivery->details_id)->where('status', '=', 1)->first();
      if($bom != null) {
        BOMDetails::where('bom_id', '=', $bom->bom_id)->update(['status' => 0]);
        $bom->status = 0;
        $bom->save();
      }*/

      return response([ 'data' => [
        'message' => 'Delivery splited successfully'/*,
        'customerOrderDetails' => $order_details*/
        ]
      ], Response::HTTP_CREATED );

    }




    public function merge(Request $request){
      $lines = $request->lines;
      if($lines != null && sizeof($lines) > 1){
        $merge_order_qty = 0;
        $merge_planned_qty = 0;
        $merged_lines = [];
        $merged_ids = [];
        $deli_st = [];
        $deli_check = null;

        for($x = 0 ; $x < sizeof($lines) ; $x++){
          $delivery = CustomerOrderDetails::find($lines[$x]);
          $merge_order_qty += $delivery['order_qty'];
          $merge_planned_qty += $delivery['planned_qty'];
          array_push($merged_lines , $delivery->line_no);
          array_push($merged_ids , $delivery->details_id);
          array_push($deli_st , $delivery->delivery_status);
        }

        $first = CustomerOrderDetails::find($lines[0]);
        $delivery_new = new CustomerOrderDetails();

        for($x = 0 ; $x < sizeof($deli_st) ; $x++)
        {
          if($deli_check != null  &&  $deli_check != $deli_st[$x])
            { $new_deli_status = 'RELEASED'; }else{$new_deli_status=$first['delivery_status'];}
              $deli_check = $deli_st[$x];
        }

        $delivery_new->order_id = $first['order_id'];
        $delivery_new->style_color = $first['style_color'];
        $delivery_new->pcd = $first['pcd'];
        $delivery_new->rm_in_date = $first['rm_in_date'];
        $delivery_new->po_no = $first['po_no'];
        $delivery_new->planned_delivery_date = $first['planned_delivery_date'];
        $delivery_new->projection_location = $first['projection_location'];
        $delivery_new->fob = $first['fob'];
        $delivery_new->country = $first['country'];
        $delivery_new->excess_presentage = $first['excess_presentage'];
        $delivery_new->ship_mode = $first['ship_mode'];
        $delivery_new->delivery_status = $new_deli_status;
        $delivery_new->order_qty = $merge_order_qty;
        $delivery_new->planned_qty = $merge_planned_qty;
        $delivery_new->line_no = $this->get_next_line_no($first->order_id);
        $delivery_new->version_no = 0;
        $delivery_new->merged_line_nos = json_encode($merged_lines);
        $delivery_new->merged_line_ids = json_encode($merged_ids);
        $delivery_new->type_created = 'GFM';
        $delivery_new->ex_factory_date = $delivery['ex_factory_date'];
        $delivery_new->ac_date = $delivery['ac_date'];
        $delivery_new->active_status = 'ACTIVE';
        $delivery_new->costing_id = $delivery['costing_id'];
        $delivery_new->fg_id = $delivery['fg_id'];
        $delivery_new->colour_type = $delivery['colour_type'];
        $delivery_new->cus_style_manual = $delivery['cus_style_manual'];
        $delivery_new->save();

        //$new_sizes = [];
        for($x = 0 ; $x < sizeof($lines) ; $x++){
          $delivery = CustomerOrderDetails::find($lines[$x]);
          $delivery->delivery_status = 'CANCELLED';
          $delivery->type_modified = 'MERGE';
          //$delivery->active_status = 'INACTIVE';
          $delivery->merge_generated_line_id = $delivery_new->details_id;
          $delivery->save();
        }

        //$ids_str = implode(',',$merged_ids);
        /*$sizes = DB::select("SELECT size_id,SUM(order_qty) AS total_order_qty,SUM(planned_qty) AS total_planned_qty FROM merc_customer_order_size
        WHERE details_id IN (".$ids_str.") GROUP BY size_id" , [$ids_str]);*/
        $sizes = DB::table('merc_customer_order_size')
                 ->select(DB::raw('size_id,SUM(order_qty) AS total_order_qty,SUM(planned_qty) AS total_planned_qty'))
                 ->whereIn('details_id', $merged_ids)
                 ->groupBy('size_id')
                 ->get();

        for($y = 0 ; $y < sizeof($sizes) ; $y++){
          $size_new = new CustomerOrderSize();

          $size_new->details_id = $delivery_new->details_id;
          $size_new->size_id = $sizes[$y]->size_id;
          $size_new->order_qty = $sizes[$y]->total_order_qty;
          $size_new->planned_qty = $sizes[$y]->total_planned_qty;
          $size_new->excess_presentage = 0;
          $size_new->line_no = $this->get_next_size_line_no($delivery_new->details_id);
          $size_new->version_no = 0;
          $size_new->save();
        }

        return response([
          'data' => [
            'status' => 'success',
            'message' => 'Lines merged successfully.'
          ]
        ] , 200);
      }
      else{
        return response([
          'data' => [
            'status' => 'error',
            'message' => 'Incorrect details'
          ]
        ] , Response::HTTP_UNPROCESSABLE_ENTITY);
      }
    }


    public function revisions(Request $request){
        $delivery = CustomerOrderDetails::find($request->details_id);
        $deliveries = [];
        if($delivery != null){
          $deliveries = CustomerOrderDetails::where('order_id', '=', $delivery->order_id)
          ->join('org_color', 'org_color.color_id', '=', 'merc_customer_order_details.style_color')
          ->join('org_country', 'org_country.country_id', '=', 'merc_customer_order_details.country')
          ->join('org_location','org_location.loc_id', '=', 'merc_customer_order_details.projection_location')
          ->select('org_location.loc_name','merc_customer_order_details.*', 'org_color.color_code', 'org_color.color_name','org_country.country_description',
          DB::raw("DATE_FORMAT(merc_customer_order_details.pcd, '%d-%b-%Y') 'pcd'"),
          DB::raw("DATE_FORMAT(merc_customer_order_details.ex_factory_date, '%d-%b-%Y') 'ex_factory_date'"),
          DB::raw("DATE_FORMAT(merc_customer_order_details.planned_delivery_date, '%d-%b-%Y') 'planned_delivery_date'"),
          DB::raw("DATE_FORMAT(merc_customer_order_details.rm_in_date, '%d-%b-%Y') 'rm_in_date'"),
          DB::raw("DATE_FORMAT(merc_customer_order_details.ac_date, '%d-%b-%Y') 'ac_date'")

          )
          ->where('line_no', '=', $delivery->line_no)
          ->get();
        }
        return response([
          'data' => $deliveries
        ]);
    }


    public function origins(Request $request){
        $delivery = CustomerOrderDetails::find($request->details_id);
        $deliveries = [];
        if($delivery != null){

          if($delivery->type_created == 'GFS'){
            $deliveries = CustomerOrderDetails::where('details_id', '=', $delivery->parent_line_id)
            ->join('org_color', 'org_color.color_id', '=', 'merc_customer_order_details.style_color')
            ->join('org_country', 'org_country.country_id', '=', 'merc_customer_order_details.country')
            ->join('org_location','org_location.loc_id', '=', 'merc_customer_order_details.projection_location')
            ->select('org_location.loc_name','merc_customer_order_details.*', 'org_color.color_code', 'org_color.color_name', 'org_country.country_description',
            DB::raw("DATE_FORMAT(merc_customer_order_details.pcd, '%d-%b-%Y') 'pcd'"),
            DB::raw("DATE_FORMAT(merc_customer_order_details.ex_factory_date, '%d-%b-%Y') 'ex_factory_date'"),
            DB::raw("DATE_FORMAT(merc_customer_order_details.planned_delivery_date, '%d-%b-%Y') 'planned_delivery_date'"),
            DB::raw("DATE_FORMAT(merc_customer_order_details.rm_in_date, '%d-%b-%Y') 'rm_in_date'"),
            DB::raw("DATE_FORMAT(merc_customer_order_details.ac_date, '%d-%b-%Y') 'ac_date'")
            )
            ->get();
          }
          else if($delivery->type_created == 'GFM'){
            $merged_lines = json_decode($delivery->merged_line_ids);
            //print_r($delivery->details_id);die();
            $deliveries = CustomerOrderDetails::whereIn('details_id', $merged_lines)
            ->join('org_color', 'org_color.color_id', '=', 'merc_customer_order_details.style_color')
            ->join('org_country', 'org_country.country_id', '=', 'merc_customer_order_details.country')
            ->join('org_location','org_location.loc_id', '=', 'merc_customer_order_details.projection_location')
            ->select('org_location.loc_name','merc_customer_order_details.*', 'org_color.color_code', 'org_color.color_name', 'org_country.country_description',
            DB::raw("DATE_FORMAT(merc_customer_order_details.pcd, '%d-%b-%Y') 'pcd'"),
            DB::raw("DATE_FORMAT(merc_customer_order_details.ex_factory_date, '%d-%b-%Y') 'ex_factory_date'"),
            DB::raw("DATE_FORMAT(merc_customer_order_details.planned_delivery_date, '%d-%b-%Y') 'planned_delivery_date'"),
            DB::raw("DATE_FORMAT(merc_customer_order_details.rm_in_date, '%d-%b-%Y') 'rm_in_date'"),
            DB::raw("DATE_FORMAT(merc_customer_order_details.ac_date, '%d-%b-%Y') 'ac_date'"))
            ->get();
          }

       }

        return response([
          'data' => $deliveries
        ]);
    }


    //check customer code already exists
    private function validate_duplicate_code($id , $code)
    {
      /*$customer = Customer::where('customer_code','=',$code)->first();
      if($customer == null){
        return ['status' => 'success'];
      }
      else if($customer->customer_id == $id){
        return ['status' => 'success'];
      }
      else {
        return ['status' => 'error','message' => 'Customer code already exists'];
      }*/
    }


    //search customer for autocomplete
    private function autocomplete_search($search)
  	{
  		$co_lists = CustomerOrderDetails::select('order_id','po_no')
  		->where([['po_no', 'like', '%'.$search.'%'],])->distinct()->get();
  		return $co_lists;
  	}
    //search po details id for mrn
    private function autocomplete_detail_id_search($search)
    {
      $co_detail_id_lists = CustomerOrderDetails::select('details_id','order_id')
      ->where([['details_id', 'like', '%'.$search.'%'],])->distinct()->get();
      return $co_detail_id_lists;
    }


    //search customer for autocomplete
    private function style_search($search)
  	{
  		$style_lists = StyleCreation::select('style_id','style_no','customer_id')
  		->where([['style_no', 'like', '%' . $search . '%'],]) ->get();
  		return $style_lists;
  	}


    private function list($order_id){
      /*$order_details = CustomerOrderDetails::join('org_color','merc_customer_order_details.style_color','=','org_color.color_id')
      ->join('org_country','merc_customer_order_details.country','=','org_country.country_id')
      ->join('org_location','merc_customer_order_details.projection_location','=','org_location.loc_id')
      ->select('merc_customer_order_details.*','org_color.color_code','org_color.color_name','org_country.country_description','org_location.loc_name')
      ->where('merc_customer_order_details.order_id','=',$order_id)
      ->get();*/
      /*$order_details = CustomerOrderDetails::with(['order_country','order_location'])
      ->where('order_id','=',$order_id)
      ->where('delivery_status' , '!=' , 'CANCEL')
      ->where(function($q) use ($order_id) {
        $q->where('version_no', function($q) use ($order_id)
          {
             $q->from('merc_customer_order_details')
              ->selectRaw('MAX(version_no)')
              ->where('order_id', '=', $order_id)
          });
      })
      ->get();*/
      $order_details = DB::select("select DATE_FORMAT(a.ac_date, '%d-%b-%Y') as ac_date_01,
      DATE_FORMAT(a.rm_in_date, '%d-%b-%Y') as rm_in_date_01,
      DATE_FORMAT(a.planned_delivery_date, '%d-%b-%Y') as planned_delivery_date_01,
      DATE_FORMAT(a.ex_factory_date, '%d-%b-%Y') as ex_factory_date_01,
      DATE_FORMAT(a.pcd, '%d-%b-%Y') as pcd_01,
       a.*,round((a.order_qty * a.fob),4) as total_value,
      org_country.country_description,org_location.loc_name,org_color.color_code,
      org_color.color_name,item_master.master_code,item_master.master_description,
      a.order_qty * product_feature.count as ord_qty_pcs
      from merc_customer_order_details a
      INNER join org_country on a.country = org_country.country_id
      INNER join org_location on a.projection_location = org_location.loc_id
      INNER join org_color on a.style_color = org_color.color_id
      INNER join item_master on a.fng_id = item_master.master_id
      INNER JOIN merc_customer_order_header ON a.order_id = merc_customer_order_header.order_id
      INNER JOIN style_creation ON merc_customer_order_header.order_style = style_creation.style_id
      INNER JOIN product_feature ON style_creation.product_feature_id = product_feature.product_feature_id
      where
      a.order_id = ? and
      #a.delivery_status != ? and
      a.type_modified is null and
      a.version_no = (select MAX(b.version_no)
      from merc_customer_order_details b where b.order_id = a.order_id and a.line_no=b.line_no)

      order by a.active_status ASC,a.line_no ASC",
      [$order_id , 'RELEASED']);
      return $order_details;
    }


    private function style_colors($style){
    /*  $colors = DB::select("SELECT costing_bulk_feature_details.combo_color, org_color.color_code,org_color.color_name FROM costing_bulk_feature_details
          INNER JOIN costing_bulk ON costing_bulk.bulk_costing_id = costing_bulk_feature_details.bulkheader_id
          INNER JOIN org_color ON costing_bulk_feature_details.combo_color = org_color.color_id
          WHERE costing_bulk.style_id = ?",[$style]);*/
      $colors = DB::select("SELECT color_id,color_code,color_name from org_color");
      return $colors;
    }


    //get searched customers for datatable plugin format
    private function datatable_search($data)
    {
      /*$start = $data['start'];
      $length = $data['length'];
      $draw = $data['draw'];
      $search = $data['search']['value'];
      $order_details = $data['order'][0];
      $order_details_column = $data['columns'][$order_details['column']]['data'];
      $order_details_type = $order_details['dir'];

      $customer_list = Customer::select('*')
      ->where('customer_code'  , 'like', $search.'%' )
      ->orWhere('customer_name'  , 'like', $search.'%' )
      ->orWhere('customer_short_name'  , 'like', $search.'%' )
      ->orderBy($order_details_column, $order_details_type)
      ->offset($start)->limit($length)->get();

      $customer_count = Customer::where('customer_code'  , 'like', $search.'%' )
      ->orWhere('customer_name'  , 'like', $search.'%' )
      ->orWhere('customer_short_name'  , 'like', $search.'%' )
      ->count();

      return [
          "draw" => $draw,
          "recordsTotal" => $customer_count,
          "recordsFiltered" => $customer_count,
          "data" => $customer_list
      ];*/
    }


    //get searched customers for datatable plugin format
    /*private function get_next_version_no($details_id)
    {
      $max_id = CustomerOrderSize::where('details_id','=',$details_id)->max('version_no');
      return ($max_id + 1);
    }*/

    private function get_delivery_details($details_id){
      $deliveries = CustomerOrderDetails::join('org_country', 'org_country.country_id', '=', 'merc_customer_order_details.country')
      ->join('org_location', 'org_location.loc_id', '=', 'merc_customer_order_details.projection_location')
      ->join('org_color', 'org_color.color_id', '=', 'merc_customer_order_details.style_color')
      ->join('item_master', 'item_master.master_id', '=', 'merc_customer_order_details.fng_id')
      ->select(DB::raw("DATE_FORMAT(merc_customer_order_details.pcd, '%d-%b-%Y') 'pcd_01'"),DB::raw("DATE_FORMAT(merc_customer_order_details.ex_factory_date, '%d-%b-%Y') 'ex_factory_date_01'"),DB::raw("DATE_FORMAT(merc_customer_order_details.planned_delivery_date, '%d-%b-%Y') 'planned_delivery_date_01'"),DB::raw("DATE_FORMAT(merc_customer_order_details.rm_in_date, '%d-%b-%Y') 'rm_in_date_01'"),DB::raw("DATE_FORMAT(merc_customer_order_details.ac_date, '%d-%b-%Y') 'ac_date_01'"),'merc_customer_order_details.*','org_country.country_description','org_location.loc_name','org_color.color_code','org_color.color_name','item_master.master_code','item_master.master_description')
      ->where('merc_customer_order_details.details_id', '=', $details_id)
      ->first();
      return $deliveries;

      //,item_master.master_code,item_master.master_description
    }


    private function get_next_line_no($order_id)
    {
      $max_no = CustomerOrderDetails::where('order_id','=',$order_id)->max('line_no');
      return ($max_no + 1);
    }


    //get searched customers for datatable plugin format
    private function get_next_size_line_no($details_id)
    {
      $max_no = CustomerOrderSize::where('details_id','=',$details_id)->max('line_no');
      return ($max_no + 1);
    }

    public function load_colour_type(Request $request){

      $style_id = $request->style_id;
      $colour_type = Costing::select('merc_color_options.col_opt_id', 'merc_color_options.color_option')
                   ->join('merc_color_options', 'costing.color_type_id', '=', 'merc_color_options.col_opt_id')
                   ->where('style_id', '=', $style_id)
                   ->groupBy('costing.color_type_id')
                   ->get();

      $arr['colour_type'] = $colour_type;

      if($arr == null)
          throw new ModelNotFoundException("Requested section not found", 1);
      else
          return response([ 'data' => $arr ]);

    }

    public function change_style_colour(Request $request){

    $style_id  = $request->style_id;
    $season_id = $request->season_id;
    $stage_id  = $request->stage_id;
    $color_t   = $request->color_t;

    $st_colour = Costing::select('org_color.color_id', 'org_color.color_code')
                 ->join('costing_finish_goods', 'costing.id', '=', 'costing_finish_goods.costing_id')
                 ->join('org_color', 'costing_finish_goods.combo_color_id', '=', 'org_color.color_id')
                 ->where('style_id', '=', $style_id)
                 ->where('bom_stage_id', '=', $stage_id)
                 ->where('season_id', '=', $season_id)
                 ->where('color_type_id', '=', $color_t)
                 ->get();

    $arr['style_colour']  = $st_colour;

    $fob = Costing::select('fob')
    ->where('style_id', '=', $style_id)
    ->where('bom_stage_id', '=', $stage_id)
    ->where('season_id', '=', $season_id)
    ->where('color_type_id', '=', $color_t)
    ->get();

    $arr['fob']  = $fob;

    if($arr == null)
      throw new ModelNotFoundException("Requested section not found", 1);
    else
      return response([ 'data' => $arr ]);

    }


    public function load_fng_colour(Request $request){

    $fng  = $request->fng;
    //item_fng_colour

    $st_colour = Item::select('org_color.color_id', 'org_color.color_code')
                 ->join('org_color', 'item_master.color_id', '=', 'org_color.color_id')
                 ->where('item_master.master_id', '=', $fng)
                 ->get();
    $arr['item_fng_colour']  = $st_colour;

    $fng_fob =  DB::table('bom_header')
                    ->select('bom_header.fob')
                    ->where('bom_header.fng_id' , '=', $fng )
                    ->get();

    $item_des =  DB::table('item_master')
                    ->select('item_master.master_description')
                    ->where('item_master.master_id' , '=', $fng )
                    ->get();

    $arr['fob'] = $fng_fob;
    $arr['descrip'] = $item_des;

    $fng_country_lists =  DB::table('bom_header')
                    ->select('org_country.country_id','org_country.country_description')
                    ->join('org_country', 'bom_header.country_id', '=', 'org_country.country_id')
                    ->where('bom_header.fng_id' , '=', $fng )
                    ->get();
    $arr['country'] = $fng_country_lists;

    if($arr == null)
      throw new ModelNotFoundException("Requested section not found", 1);
    else
      return response([ 'data' => $arr ]);

    }


    public function load_fng(Request $request){

    $style_id  = $request->style_id;
    $season_id = $request->season_id;
    $stage_id  = $request->stage_id;
    $color_t   = $request->color_t;

    $st_colour = Costing::select('item_master.master_id','item_master.master_code','item_master.master_description','org_color.color_id', 'org_color.color_code')
                 ->join('bom_header', 'costing.id', '=', 'bom_header.costing_id')
                 ->join('item_master', 'bom_header.fng_id', '=', 'item_master.master_id')
                 ->join('org_color', 'item_master.color_id', '=', 'org_color.color_id')
                 ->where('style_id', '=', $style_id)
                 ->where('bom_stage_id', '=', $stage_id)
                 ->where('season_id', '=', $season_id)
                 ->where('color_type_id', '=', $color_t)
                 ->get();



    $arr['item_fng_details']  = $st_colour;

    $fob = Costing::select('fob')
    ->where('style_id', '=', $style_id)
    ->where('bom_stage_id', '=', $stage_id)
    ->where('season_id', '=', $season_id)
    ->where('color_type_id', '=', $color_t)
    ->get();

    $arr['fob']  = $fob;

    if($arr == null)
      throw new ModelNotFoundException("Requested section not found", 1);
    else
      return response([ 'data' => $arr ]);

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
