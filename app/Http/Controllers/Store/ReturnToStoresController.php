<?php

namespace App\Http\Controllers\Store;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Libraries\AppAuthorize;
use App\Libraries\CapitalizeAllFields;
use App\Libraries\UniqueIdGenerator;
use Exception;

use App\Models\stores\RollPlan;
use App\Models\Store\TrimPacking;

use App\Models\Store\StockTransaction;
use App\Models\Store\ReturnToStoreHeader;
use App\Models\Store\ReturnToStoreDetails;
use App\Models\Org\ConversionFactor;
use App\Models\Store\Stock;
use App\Models\Merchandising\ShopOrderDetail;

class ReturnToStoresController extends Controller
{ 

    public function index(Request $request)
    {
      $type = $request->type;
      if($type == 'load_details') {
        $data = $request->all();
        $this->load_details($data);
      }else if($type == 'datatable'){
        $data = $request->all();
        $this->datatable_search($data);
      }
    }

    private function datatable_search($data)
    {

        $start = $data['start'];
        $length = $data['length'];
        $draw = $data['draw'];
        $search = $data['search']['value'];
        $order = $data['order'][0];
        $order_column = $data['columns'][$order['column']]['data'];
        $order_type = $order['dir'];

        $list = ReturnToStoreHeader::join('store_return_to_store_detail','store_return_to_store_header.return_id','=','store_return_to_store_detail.return_id')
        ->join('item_master','store_return_to_store_detail.item_id','=','item_master.master_id')
        ->join('usr_login','store_return_to_store_header.created_by','=','usr_login.user_id')
        ->join('org_location','store_return_to_store_header.user_loc_id','=','org_location.loc_id')
        ->join('store_issue_header','store_return_to_store_header.issue_id','=','store_issue_header.issue_id')
        ->select('store_return_to_store_header.return_no',
        'store_issue_header.issue_no',
        'store_return_to_store_detail.*',
        'item_master.master_code',
        'item_master.master_description',
        'usr_login.user_name',
        'org_location.loc_name')
        ->where('store_return_to_store_header.return_no' , 'like', $search.'%' )
        ->orWhere('item_master.master_code' , 'like', $search.'%' )
        ->orWhere('item_master.master_description' , 'like', $search.'%' )
        ->orWhere('org_location.loc_name','like',$search.'%')
        ->orWhere('usr_login.user_name','like',$search.'%')
        ->orWhere('store_issue_header.issue_no','like',$search.'%')
        ->orderBy($order_column, $order_type)
        ->offset($start)->limit($length)->get();


        $count = ReturnToStoreHeader::join('store_return_to_store_detail','store_return_to_store_header.return_id','=','store_return_to_store_detail.return_id')
        ->join('item_master','store_return_to_store_detail.item_id','=','item_master.master_id')
        ->join('usr_login','store_return_to_store_header.created_by','=','usr_login.user_id')
        ->join('org_location','store_return_to_store_header.user_loc_id','=','org_location.loc_id')
        ->join('store_issue_header','store_return_to_store_header.issue_id','=','store_issue_header.issue_id')
        ->select('store_return_to_store_header.return_no',
        'store_issue_header.issue_no',
        'store_return_to_store_detail.*',
        'item_master.master_code',
        'item_master.master_description',
        'usr_login.user_name',
        'org_location.loc_name')
        ->where('store_return_to_store_header.return_no' , 'like', $search.'%' )
        ->orWhere('item_master.master_code' , 'like', $search.'%' )
        ->orWhere('item_master.master_description' , 'like', $search.'%' )
        ->orWhere('org_location.loc_name','like',$search.'%')
        ->orWhere('usr_login.user_name','like',$search.'%')
        ->orWhere('store_issue_header.issue_no','like',$search.'%')
        ->count();

        echo json_encode([
          "draw" => $draw,
          "recordsTotal" => $count,
          "recordsFiltered" => $count,
          "data" => $list
        ]);
      
    }

    public function load_issue_details(Request $request)
    {
        $issue_no = $request['search']['issue_no']['issue_id'];
        $roll_from = $request['details']['roll_from'];
        $roll_to = $request['details']['roll_to'];
        $lab_comments = $request['details']['lab_comments'];
        $shade = $request['details']['shade'];
        $item_code = $request['details']['item_code']['master_code'];
        $batch = $request['details']['batch']['batch_no'];
        $ins_status_code = $request['details']['ins_status']['status_name'];

        //Fabric
        $fabric = DB::table('store_issue_header')
        ->join('store_issue_detail','store_issue_header.issue_id','=','store_issue_detail.issue_id')
        ->join('org_location','store_issue_detail.location_id','=','org_location.loc_id')
        ->join('org_store','store_issue_detail.store_id','=','org_store.store_id')
        ->join('org_substore','store_issue_detail.sub_store_id','=','org_substore.substore_id')
        ->join('org_store_bin','store_issue_detail.bin','=','org_store_bin.store_bin_id')
        ->join('item_master','store_issue_detail.item_id','=','item_master.master_id')
        ->join('item_category','item_master.category_id','=','item_category.category_id')
        ->join('store_roll_plan','store_issue_detail.item_detail_id','=','store_roll_plan.roll_plan_id')
        ->join('store_mrn_detail','store_issue_detail.mrn_detail_id','=','store_mrn_detail.mrn_detail_id')
        ->join('store_mrn_header','store_mrn_detail.mrn_id','=','store_mrn_header.mrn_id')
        ->join('store_grn_detail','store_roll_plan.grn_detail_id','=','store_grn_detail.grn_detail_id')
        ->join('org_uom','store_mrn_detail.uom','=','org_uom.uom_id')
        ->leftJoin('store_fabric_inspection','store_roll_plan.roll_plan_id','=','store_fabric_inspection.roll_plan_id')
        ->select('store_issue_header.*',
        'store_issue_detail.issue_detail_id',
        'store_issue_detail.mrn_detail_id',
        'store_issue_detail.item_id',
        'store_issue_detail.qty',
        'store_issue_detail.location_id',
        'store_issue_detail.store_id',
        'store_issue_detail.sub_store_id',
        'store_issue_detail.bin',
        'store_issue_detail.user_loc_id',
        'store_issue_detail.item_detail_id',
        'org_location.loc_name',
        'org_store.store_name',
        'org_substore.substore_name',
        'org_store_bin.store_bin_name',
        'item_master.master_code',
        'item_master.master_description',
        'item_master.standard_price',
        'item_master.category_id',
        'item_category.category_code',
        'store_issue_detail.mrn_detail_id',
        'store_mrn_detail.shop_order_id',
        'store_mrn_detail.shop_order_detail_id',
        'store_mrn_detail.cust_order_detail_id',
        'store_mrn_detail.color_id',
        'store_mrn_detail.size_id',
        'item_master.inventory_uom',
        'org_uom.uom_code AS uom_description',
        'store_mrn_detail.uom AS request_uom',
        'store_mrn_header.style_id',
        'store_grn_detail.grn_detail_id',
        'store_grn_detail.standard_price',
        'store_grn_detail.purchase_price',
        'store_roll_plan.grn_detail_id',
        'store_roll_plan.lot_no',
        'store_roll_plan.batch_no',
        'store_roll_plan.roll_no AS roll_box',
        'store_roll_plan.bin',
        'store_roll_plan.shade',
        'store_fabric_inspection.inspection_status',
        'store_fabric_inspection.shade AS ins_shade',
        'store_fabric_inspection.lab_comment'
        );
        $fabric->where('store_issue_header.issue_id', $issue_no);
        $fabric->where('item_category.category_code', 'FAB');
        $fabric->where('store_issue_detail.qty', '>', 0);
        //Filters
        if(($roll_from!=null || $roll_from!="") && ($roll_to!=null || $roll_to!="")){
            $fabric->whereBetween('store_roll_plan.roll_no', [$roll_from,$roll_to]);
        }
        else if(($roll_from!=null || $roll_from!="")){
            $fabric->where('store_roll_plan.roll_no', $roll_from);
        }
        if($item_code!=null || $item_code!=""){
            $fabric->where('item_master.master_code', $item_code);
        }
        if($batch!=null || $batch!=""){
            $fabric->where('store_roll_plan.batch_no', $batch);
        }
        if($shade!=null || $shade!=""){
            $fabric->where('store_roll_plan.shade', 'like', '%' . $shade . '%');
        }
        if($ins_status_code!=null || $ins_status_code!=""){
            $fabric->where('store_fabric_inspection.inspection_status', $ins_status_code);
        }
        if($lab_comments!=null || $lab_comments!=""){
            $fabric->where('store_fabric_inspection.lab_comment', 'like', '%' . $lab_comments . '%');
        }
        //Trims
        $trim = DB::table('store_issue_header')
        ->join('store_issue_detail','store_issue_header.issue_id','=','store_issue_detail.issue_id')
        ->join('org_location','store_issue_detail.location_id','=','org_location.loc_id')
        ->join('org_store','store_issue_detail.store_id','=','org_store.store_id')
        ->join('org_substore','store_issue_detail.sub_store_id','=','org_substore.substore_id')
        ->join('org_store_bin','store_issue_detail.bin','=','org_store_bin.store_bin_id')
        ->join('item_master','store_issue_detail.item_id','=','item_master.master_id')
        ->join('item_category','item_master.category_id','=','item_category.category_id')
        ->join('store_trim_packing_detail','store_issue_detail.item_detail_id','=','store_trim_packing_detail.trim_packing_id')
        ->join('store_mrn_detail','store_issue_detail.mrn_detail_id','=','store_mrn_detail.mrn_detail_id')
        ->join('store_mrn_header','store_mrn_detail.mrn_id','=','store_mrn_header.mrn_id')
        ->join('store_grn_detail','store_trim_packing_detail.grn_detail_id','=','store_grn_detail.grn_detail_id')
        ->join('org_uom','store_mrn_detail.uom','=','org_uom.uom_id')
        ->select('store_issue_header.*',
        'store_issue_detail.issue_detail_id',
        'store_issue_detail.mrn_detail_id',
        'store_issue_detail.item_id',
        'store_issue_detail.qty',
        'store_issue_detail.location_id',
        'store_issue_detail.store_id',
        'store_issue_detail.sub_store_id',
        'store_issue_detail.bin',
        'store_issue_detail.user_loc_id',
        'store_issue_detail.item_detail_id',
        'org_location.loc_name',
        'org_store.store_name',
        'org_substore.substore_name',
        'org_store_bin.store_bin_name',
        'item_master.master_code',
        'item_master.master_description',
        'item_master.standard_price',
        'item_master.category_id',
        'item_category.category_code',
        'store_issue_detail.mrn_detail_id',
        'store_mrn_detail.shop_order_id',
        'store_mrn_detail.shop_order_detail_id',
        'store_mrn_detail.cust_order_detail_id',
        'store_mrn_detail.color_id',
        'store_mrn_detail.size_id',
        'item_master.inventory_uom',
        'org_uom.uom_code AS uom_description',
        'store_mrn_detail.uom AS request_uom',
        'store_mrn_header.style_id',
        'store_grn_detail.grn_detail_id',
        'store_grn_detail.standard_price',
        'store_grn_detail.purchase_price',
        'store_trim_packing_detail.grn_detail_id',
        'store_trim_packing_detail.lot_no',
        'store_trim_packing_detail.batch_no',
        'store_trim_packing_detail.box_no AS roll_box',
        'store_trim_packing_detail.bin',
        'store_trim_packing_detail.shade',
        DB::raw("(NULL) AS inspection_status"),
        DB::raw("(NULL) AS ins_shade"),
        DB::raw("(NULL) AS lab_comment")
        );
        $trim->where('store_issue_header.issue_id', $issue_no);
        $trim->where('item_category.category_code' ,'<>', 'FAB');
        $trim->where('store_issue_detail.qty', '>', 0);

        if(($roll_from!=null || $roll_from!="") && ($roll_to!=null || $roll_to!="")){
            $trim->whereBetween('store_trim_packing_detail.box_no', [$roll_from,$roll_to]);
        }
        else if(($roll_from!=null || $roll_from!="")){
            $trim->where('store_trim_packing_detail.box_no', $roll_from);
        }
        if($item_code!=null || $item_code!=""){
            $trim->where('item_master.master_code', $item_code);
        }
        if($batch!=null || $batch!=""){
            $trim->where('store_trim_packing_detail.batch_no', $batch);
        }
        
        $trim->unionAll($fabric);
        $data = $trim->get();

        echo json_encode([
            "recordsTotal" => "",
            "recordsFiltered" => "",
            "data" => $data
        ]);

    }

    public function store(Request $request)
    {

        $return_no = UniqueIdGenerator::generateUniqueId('RE_STORE', auth()->payload()['loc_id']);
        $header_data = array(
            "issue_id" => $request->header['issue_no']['issue_id'], 
            "status" => 1,
            "return_no" => $return_no
        );

        $save_header = new ReturnToStoreHeader();
        if($save_header->validate($header_data))
        {
          $save_header->fill($header_data);
          $save_header->save();

          if($save_header){
            
            $save_details = $this->save_return_details($save_header['return_id'],$request['details']);
            $save_stock_transaction = $this->save_stock_transaction($save_header['return_id'],$request['details']);
            $update_roll_plan = $this->update_roll_plan($save_header['return_id'],$request['details']);
            $update_store_stock = $this->update_store_stock($save_header['return_id'],$request['details']);
            $update_shop_order_qty = $this->update_shop_order_qty($save_header['return_id'],$request['details']);
            
            return response([ 'data' => [
                'message' => 'Data saved success',
                'id' => $save_header['return_id'],
                'status'=> 'success'
                ]
            ], Response::HTTP_CREATED );

          }else{

            return response([ 'data' => [
                'message' => 'Data saving failed',
                'id' => '',
                'status'=> 'fail'
                ]
            ], Response::HTTP_CREATED );
          }
          
        }
        else
        {
          $errors = $save_header->errors();// failure, get errors
          $errors_str = $save_header->errors_tostring();
          return response(['errors' => ['validationErrors' => $errors, 'validationErrorsText' => $errors_str]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

    }
    
    public function save_return_details($return_id,$data){

        foreach($data as $row){

            if(!isset($row['comments'])){
              $comments = "";
            }else{
              $comments = $row['comments'];
            }

            $detail_data = array(
            'return_id' => $return_id,
            'item_id' => $row['item_id'],
            'inv_uom' => $row['inventory_uom'],
            'request_uom' => $row['request_uom'],
            'issue_qty' => $row['qty'],
            'return_qty' => $row['return_qty'],
            'status' => 1,
            'location_id' => $row['location_id'],
            'store_id' => $row['store_id'],
            'sub_store_id' => $row['sub_store_id'],
            'bin' => $row['bin'],
            'roll_box' => $row['roll_box'],
            'batch_no' => $row['batch_no'],
            'shade' => $row['shade'],
            'item_detail_id' => $row['item_detail_id'],
            'comments' => $comments
            );

            $save_detail = new ReturnToStoreDetails();
            if($save_detail->validate($detail_data))
            {
              $save_detail->fill($detail_data);
              $save_detail->save();  
            }
            else
            {
              $errors = $save_detail->errors();// failure, get errors
              $errors_str = $save_detail->errors_tostring();
              return response(['errors' => ['validationErrors' => $errors, 'validationErrorsText' => $errors_str]], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

        }

    }

    public function save_stock_transaction($return_id,$data){

        foreach($data as $row){

            if($row['inventory_uom']==$row['request_uom']){
               $qty = $row['return_qty'];
            }else{
               $qty = $this->convert_into_inventory_uom($row['request_uom'],$row['inventory_uom'],$row['return_qty']);
            }

            $st = new StockTransaction;
            $st->doc_num = $return_id;
            $st->doc_type = 'RETURN_TO_STORE';
            $st->style_id = $row['style_id'];
            $st->customer_po_id = $row['cust_order_detail_id'];
            $st->size = $row['size_id'];
            $st->color = $row['color_id'];
            $st->main_store = $row['store_id'];
            $st->sub_store = $row['sub_store_id'];
            $st->location = $row['location_id'];
            $st->bin = $row['bin'];
            $st->status = 'CONFIRM';
            $st->shop_order_id = $row['shop_order_id'];
            $st->shop_order_detail_id = $row['shop_order_detail_id'];
            $st->direction = '+';
            $st->item_code = $row['item_id'];
            $st->qty = $qty;
            $st->uom = $row['inventory_uom'];
            $st->standard_price = $row['standard_price'];
            $st->purchase_price = $row['purchase_price'];
            $st->created_by = auth()->payload()['user_id'];
            $st->save();
        }

    }
    
    public function update_roll_plan($return_id,$data){

        foreach($data as $row){

            if($row['inventory_uom']==$row['request_uom']){
               $return_qty = $row['return_qty'];
            } else {
               $return_qty = $this->convert_into_inventory_uom($row['request_uom'],$row['inventory_uom'],$row['return_qty']);
            }

            if($row['category_code']=='FAB'){
                $available_qty=RollPlan::where('roll_plan_id','=',$row['item_detail_id'])->pluck('qty'); 
                $update = RollPlan::where('roll_plan_id', $row['item_detail_id'])->update(['qty' => ($available_qty[0]+$return_qty) ]);
            } else {
               $available_qty=TrimPacking::where('trim_packing_id','=',$row['item_detail_id'])->pluck('qty'); 
               $update = TrimPacking::where('trim_packing_id', $row['item_detail_id'])->update(['qty' => ($available_qty[0]+$return_qty) ]);
            }

        }

    }

    public function update_store_stock($return_id,$data){

        foreach($data as $row){

            if($row['inventory_uom']==$row['request_uom']){
               $return_qty = $row['return_qty'];
            } else {
               $return_qty = $this->convert_into_inventory_uom($row['request_uom'],$row['inventory_uom'],$row['return_qty']);
            }

            $stock_line=Stock::where('store_stock.item_id',$row['item_id'])
            ->where('store_stock.shop_order_id',$row['shop_order_id'])
            ->Where('store_stock.shop_order_detail_id',$row['shop_order_detail_id'])
            ->where('store_stock.style_id',$row['style_id'])
            ->Where('store_stock.bin',$row['bin'])
            ->where('store_stock.store',$row['store_id'])
            ->Where('store_stock.sub_store',$row['sub_store_id'])
            ->where('store_stock.location',$row['location_id'])
            ->first();

            if($stock_line){
               $update = Stock::where('id', $stock_line->id)->update(['qty' => ($stock_line->qty+$return_qty) ]);
            }

        }

    }

    public function convert_into_inventory_uom($request_uom,$inventory_uom,$qty){
        $unit_code=UOM::where('uom_id','=',$inventory_uom)->pluck('uom_code');
        $base_unit_code=UOM::where('uom_id','=',$request_uom)->pluck('uom_code');
        
        $con=ConversionFactor::select('*')
        ->where('unit_code','=',$unit_code[0])
        ->where('base_unit','=',$base_unit_code[0])
        ->first();

        return ($qty*$con->present_factor);     
    }

    public function update_shop_order_qty($return_id,$data){
        foreach($data as $row){
            $available_qty=ShopOrderDetail::where('shop_order_detail_id','=',$row['shop_order_detail_id'])->pluck('asign_qty'); 
            $update = ShopOrderDetail::where('shop_order_detail_id', $row['shop_order_detail_id'])->update(['asign_qty' => ($available_qty[0]-$row['return_qty']) ]);            
        }
    }

}
