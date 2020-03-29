<?php

namespace App\Http\Controllers\Merchandising;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use App\Models\Merchandising\PoOrderHeader;
//use App\Libraries\UniqueIdGenerator;
use App\Models\Merchandising\BOMDetails;
use App\Models\Merchandising\PurchaseReqLines;

use App\Models\Merchandising\ShopOrderHeader;
use App\Models\Merchandising\ShopOrderDetail;
use App\Models\Merchandising\ShopOrderDelivery;
use App\Models\Merchandising\Item\Item;
use App\Models\Org\UOM;

class PurchaseOrderManualController extends Controller
{
    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => ['index']]);
    }

    //get customer list
    public function index(Request $request)
    {
      //$id_generator = new UniqueIdGenerator();
      //echo $id_generator->generateCustomerOrderId('CUSTOMER_ORDER' , 1);
      //echo UniqueIdGenerator::generateUniqueId('CUSTOMER_ORDER' , 2 , 'FDN');
      $type = $request->type;
      if($type == 'datatable') {
        $data = $request->all();
        return response($this->datatable_search($data));
      }
      if($type == 'datatable_2') {
        $data = $request->all();
        return response($this->datatable_2_search($data));
      }
      else if($type == 'auto')    {
        $search = $request->search;
        return response($this->autocomplete_search($search));
      }
      else if($type == 'loadPurchaseUom')    {
        $search = $request->search;
        return response($this->autocomplete_PurchaseUom_search($search));
      }
      else if($type == 'style')    {
        $search = $request->search;
        return response($this->style_search($search));
      }
      else{
        return response([]);
      }
    }


    //create a customer
    public function store(Request $request)
    {
      $order = new PoOrderHeader();
      if($order->validate($request->all()))
      {
        //dd($request);
        $order->fill($request->all());
        $order->po_type = $request->po_type['bom_stage_id'];
        $order->ship_mode = $request->ship_mode['ship_mode'];
        $order->special_ins = strtoupper($request->special_ins);
        $order->purchase_uom = $request->purchase_uom['uom_id'];
        $order->status = '1';
        $order->po_status = 'PLANNED';
        $order->save();

        $order_id=$order->po_id;
        $po_num  =$order->po_number;
        $prl_id  =$order->prl_id;

        $current_value = DB::select("SELECT ER.rate FROM merc_po_order_header AS PH
                INNER JOIN org_exchange_rate AS ER ON PH.po_def_cur = ER.currency WHERE
                ER.`status` = 1 AND PH.po_id = '$order_id' ORDER BY ER.id DESC LIMIT 0, 1");

        $orgin = DB::select("SELECT POH.prl_id,POL.origin_type_id FROM
                 merc_po_order_header AS POH INNER JOIN merc_purchase_req_lines AS POL ON POH.prl_id = POL.merge_no
                 WHERE POH.prl_id = '$prl_id' AND POH.po_id = '$order_id' GROUP BY POL.origin_type_id ");

        if($orgin[0]->origin_type_id == '1'){
          $new_po = 'I-'.$po_num;
        }else if($orgin[0]->origin_type_id == '2'){
          $new_po = 'L-'.$po_num;
        }
        $cur_update=PoOrderHeader::find($order_id);
        $cur_update->cur_value=$current_value[0]->rate;
        $cur_update->po_number=$new_po;
        $cur_update->save();

        $load_poh_status = DB::select("SELECT MOPH.prl_id FROM merc_po_order_header AS MOPH
                WHERE MOPH.po_id = '$order_id'");

        //$update_status=PurchaseReqLines::where('merge_no', $load_poh_status[0]->prl_id);
        //              ->update(['status_user' => 'HOLD']);

        DB::table('merc_purchase_req_lines')
            ->where('merge_no', $load_poh_status[0]->prl_id)
            ->update(['status_user' => 'HOLD']);

        DB::table('merc_purchase_req_lines')
            ->join('merc_shop_order_detail', 'merc_shop_order_detail.shop_order_detail_id', '=', 'merc_purchase_req_lines.shop_order_detail_id')
            ->where('merge_no', $load_poh_status[0]->prl_id)
            ->update(['po_status' => 'HOLD','po_con' => 'CREATE']);

        return response([ 'data' => [
          'message' => 'Purchase order was saved successfully',
          'savepo' => $order,
          'newpo' => $new_po,
          'status' => 'PLANNED'
          ]
        ], Response::HTTP_CREATED );
      }
      else
      {
          $errors = $order->errors();// failure, get errors
          return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
      }
    }


    //get a customer
    public function show($id)
    {
      $customer = PoOrderHeader::with(['currency','location','supplier'])->find($id);
      if($customer == null)
        throw new ModelNotFoundException("Requested PO not found", 1);
      else
        return response([ 'data' => $customer ]);
    }


    //update a customer
   public function update(Request $request, $id)
    {
      //dd($request->po_number);
      $pOrder = PoOrderHeader::find($id);
      if($pOrder->validate($request->all()))
      {
        $pOrder->fill($request->except('customer_code'));
        $pOrder->special_ins = strtoupper($request->special_ins);
        $pOrder->po_status = 'PLANNED';
        $pOrder->save();

        $current_value = DB::select("SELECT ER.rate FROM merc_po_order_header AS PH
                INNER JOIN org_exchange_rate AS ER ON PH.po_def_cur = ER.currency WHERE
                ER.`status` = 1 AND PH.po_id = '$id' ORDER BY ER.id DESC LIMIT 0, 1");

        //print_r($current_value);
        $cur_update=PoOrderHeader::find($id);
        $cur_update->cur_value=$current_value[0]->rate;
        $cur_update->save();

        return response([ 'data' => [
          'message' => 'Purchase order was updated successfully',
          'customer' => $pOrder,
          'savepo' => $pOrder,
          'newpo' => $request->po_number
        ]]);
      }
      else
      {
        $errors = $pOrder->errors();// failure, get errors
        return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
      }
    }


    //deactivate a customer
    public function destroy($id)
    {
      /*$customer = Customer::where('customer_id', $id)->update(['status' => 0]);
      return response([
        'data' => [
          'message' => 'Customer was deactivated successfully.',
          'customer' => $customer
        ]
      ] , Response::HTTP_NO_CONTENT);*/
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

    public function save_customer_divisions(Request $request)
    {
      /*$customer_id = $request->get('customer_id');
      $divisions = $request->get('divisions');
      if($customer_id != '')
      {
        DB::table('org_customer_divisions')->where('customer_id', '=', $customer_id)->delete();
        $customer = Customer::find($customer_id);
        $save_divisions = array();

        foreach($divisions as $devision)		{
          array_push($save_divisions,Division::find($devision['division_id']));
        }

        $customer->divisions()->saveMany($save_divisions);
        return response([
          'data' => [
            'customer_id' => $customer_id
          ]
        ]);
      }
      else {
        throw new ModelNotFoundException("Requested customer not found", 1);
      }*/
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
    private function autocomplete_PurchaseUom_search($search)
  	{
  		// $customer_lists = Item::select('org_uom.uom_id','org_uom.uom_code')
      // ->join('item_category', 'item_master.category_id', '=', 'item_category.category_id')
      // ->join('item_uom', 'item_master.master_id', '=', 'item_uom.master_id')
      // ->join('org_uom', 'item_uom.uom_id', '=', 'org_uom.uom_id')
  		// ->where([['item_category.category_name', '=', 'FABRIC'],['org_uom.uom_code', 'like', '%' . $search . '%']])
      // ->groupBy('org_uom.uom_code')
      // ->orderBy('org_uom.uom_id', 'ASC')
      // ->get();

      $customer_lists = UOM::select('org_uom.uom_id','org_uom.uom_code')
  		->where([['org_uom.conversion_factor', '=', '1'],['org_uom.uom_code', 'like', '%' . $search . '%']])
      ->get();
  		return $customer_lists;
  	}


    //search customer for autocomplete
    private function style_search($search)
  	{
  	/*	$style_lists = StyleCreation::select('style_id','style_no','customer_id')
  		->where([['style_no', 'like', '%' . $search . '%'],]) ->get();
  		return $style_lists;*/
  	}


    //get searched customers for datatable plugin format
    private function datatable_search($data)
    {
      $start = $data['start'];
      $length = $data['length'];
      $draw = $data['draw'];
      $search = $data['search']['value'];
      $order = $data['order'][0];
      $order_column = $data['columns'][$order['column']]['data'];
      $order_type = $order['dir'];
      $user = auth()->user();



      $customer_list = PoOrderHeader::join('org_location', 'org_location.loc_id', '=', 'merc_po_order_header.po_deli_loc')
	    ->join('org_supplier', 'org_supplier.supplier_id', '=', 'merc_po_order_header.po_sup_code')
      ->join('fin_currency', 'fin_currency.currency_id', '=', 'merc_po_order_header.po_def_cur')
      ->join('merc_bom_stage', 'merc_bom_stage.bom_stage_id', '=', 'merc_po_order_header.po_type')
      ->join('usr_profile', 'usr_profile.user_id', '=', 'merc_po_order_header.created_by')

	    ->select('merc_po_order_header.*','org_location.loc_name','org_supplier.supplier_name',
          'fin_currency.currency_code','merc_bom_stage.bom_stage_description',
          DB::raw("DATE_FORMAT(merc_po_order_header.po_date, '%d-%b-%Y') AS new_date"),'usr_profile.first_name'

          )
      ->Where('merc_po_order_header.created_by','=', $user->user_id)
      ->Where(function ($query) use ($search) {
  			$query->orWhere('po_number', 'like', $search.'%')
  				    ->orWhere('supplier_name', 'like', $search.'%')
  				    ->orWhere('loc_name', 'like', $search.'%');
  		        })
      ->orderBy($order_column, $order_type)
      ->offset($start)->limit($length)->get();

      $customer_count = PoOrderHeader::join('org_location', 'org_location.loc_id', '=', 'merc_po_order_header.po_deli_loc')
	    ->join('org_supplier', 'org_supplier.supplier_id', '=', 'merc_po_order_header.po_sup_code')
      ->join('fin_currency', 'fin_currency.currency_id', '=', 'merc_po_order_header.po_def_cur')
      ->Where('merc_po_order_header.created_by','=', $user->user_id)
      ->Where(function ($query) use ($search) {
        $query->orWhere('po_number', 'like', $search.'%')
              ->orWhere('supplier_name', 'like', $search.'%')
              ->orWhere('loc_name', 'like', $search.'%');
              })
      ->count();



      return [
          "draw" => $draw,
          "recordsTotal" => $customer_count,
          "recordsFiltered" => $customer_count,
          "data" => $customer_list
      ];
    }

    private function datatable_2_search($data)
    {
      $start = $data['start'];
      $length = $data['length'];
      $draw = $data['draw'];
      $search = $data['search']['value'];
      $order = $data['order'][0];
      $order_column = $data['columns'][$order['column']]['data'];
      $order_type = $order['dir'];
      $user = auth()->user();
    //  dd($user);

      $customer_list = PurchaseReqLines::join('usr_profile', 'usr_profile.user_id', '=', 'merc_purchase_req_lines.created_by')
      ->select('merc_purchase_req_lines.merge_no as prl_id','merc_purchase_req_lines.status_user as po_status','usr_profile.first_name'
      ,DB::raw("GROUP_CONCAT(merc_purchase_req_lines.shop_order_detail_id) AS bom_lines"),
       DB::raw("DATE_FORMAT(merc_purchase_req_lines.created_date, '%d-%b-%Y') AS cd")
       )
      ->where('status_user'  , '=', 'OPEN' )
      ->Where('merc_purchase_req_lines.created_by','=', $user->user_id)
      ->orderBy($order_column, $order_type)
      ->groupBy('merge_no')
      ->offset($start)->limit($length)->get();

      //echo $customer_list;
      //die();

      $customer_count = PurchaseReqLines::join('usr_profile', 'usr_profile.user_id', '=', 'merc_purchase_req_lines.created_by')
      ->select('merc_purchase_req_lines.merge_no as prl_id','merc_purchase_req_lines.status_user as po_status','merc_purchase_req_lines.created_date','usr_profile.first_name'
      ,DB::raw("GROUP_CONCAT(merc_purchase_req_lines.shop_order_detail_id) AS bom_lines"),
       DB::raw("DATE_FORMAT(merc_purchase_req_lines.created_date, '%d-%b-%Y') AS cd")
       )
      ->where('status_user'  , '=', 'OPEN' )
      ->Where('merc_purchase_req_lines.created_by','=', $user->user_id)
      ->groupBy('merge_no')
      ->count();

      return [
          "draw" => $draw,
          "recordsTotal" => $customer_count,
          "recordsFiltered" => $customer_count,
          "data" => $customer_list
      ];
    }




    public function load_bom_Details(Request $request)
  	{

    $stage              = $request->bom_stage_id['bom_stage_id'];
    $customer_name      = $request->customer['customer_name'];
    $customer_division  = $request->division;
    $style_no           = $request->style['style_no'];
    $category           = $request->category['category_id'];
    $sub_category       = $request->sub_category['subcategory_id'];
    $supplier           = $request->supplier['supplier_id'];
    $lot                = $request->lot_number['lot_number'];
    $location           = $request->projection_location;

    if(isset($lot) == ''){ $lot_des = ""; }else{$lot_des = "merc_customer_order_header.lot_number LIKE '%".$lot."%' AND";}
    if(isset($style_no) == ''){ $style_no_des = ""; }else{$style_no_des = "style_creation.style_no LIKE '%".$style_no."%' AND";}
    if(isset($category) == null){ $category_des = ""; }else{$category_des = "item_category.category_id  LIKE '%".$category."%' AND";}
    if(isset($sub_category) == null){ $sub_category_des = ""; }else{$sub_category_des = "item_subcategory.subcategory_id  LIKE '%".$sub_category."%' AND";}
    if(isset($supplier) == ''){ $supplier_des = ""; }else{$supplier_des = "org_supplier.supplier_id LIKE '%".$supplier."%' AND";}
    //dd($request);

    $load_list['CREAT'] = DB::select("SELECT
                    merc_shop_order_header.shop_order_id,
                    merc_shop_order_detail.shop_order_detail_id,
                    merc_shop_order_detail.bom_id,
                    merc_shop_order_delivery.delivery_id,
                    merc_customer_order_header.order_stage,
                    cust_customer.customer_name,
                    style_creation.style_no,
                    merc_customer_order_header.order_code,
                    merc_customer_order_details.po_no,
                    merc_customer_order_details.pcd,
                    DATE_FORMAT(merc_customer_order_details.pcd, '%d-%b-%Y') as pcd_01,
                    merc_customer_order_details.fng_id,
                    FNG.master_code AS fng_number,
                    MAT.master_code AS mat_code,
                    MAT.master_description,
                    STY_COLOUR.color_id,
                    STY_COLOUR.color_name,
                    MAT_COL.color_id AS material_color_id,
                    MAT_COL.color_name AS material_color,
                    MAT_SIZE.size_id,
                    MAT_SIZE.size_name,
                    org_uom.uom_id,
                    org_uom.uom_code,
                    org_supplier.supplier_id,
                    org_supplier.supplier_name,
                    merc_shop_order_detail.unit_price,
                    merc_customer_order_details.order_qty,
                    merc_shop_order_detail.gross_consumption,
                    ( ROUND((merc_customer_order_details.order_qty*merc_shop_order_detail.gross_consumption),4)) AS total_qty,
                    MAT.moq,
                    MAT.mcq,
                    org_location.loc_id,
                    org_location.loc_name,
                    (SELECT Count(EX.currency) AS ex_rate
                      FROM org_exchange_rate AS EX
                      WHERE EX.currency = org_supplier.currency ) AS ex_rate,
                    merc_customer_order_details.ship_mode,
                    org_origin_type.origin_type_id,
                    org_origin_type.origin_type,
                    item_category.category_id,
                    item_category.category_name,
                    IFNULL(merc_shop_order_detail.po_qty,0) as req_qty,
                    round(IFNULL(merc_shop_order_detail.po_balance_qty,0),4) AS po_balance_qty,
                    merc_shop_order_detail.inventory_part_id,
                    ( SELECT GROUP_CONCAT( DISTINCT MPOD.po_no SEPARATOR ' | ' )AS po_nos
                      FROM merc_po_order_details AS MPOD WHERE
	                    MPOD.shop_order_id = merc_shop_order_header.shop_order_id
                      AND MPOD.shop_order_detail_id = merc_shop_order_detail.shop_order_detail_id
                    )AS po_nos,
										merc_customer_order_details.type_created,
                    bom_details.sfg_id,
                    bom_details.sfg_code,
                    SFG_COLOR.color_name as SFG_COL
                    FROM
                    merc_shop_order_header
                    INNER JOIN merc_shop_order_detail ON merc_shop_order_header.shop_order_id = merc_shop_order_detail.shop_order_id
                    INNER JOIN merc_shop_order_delivery ON merc_shop_order_header.shop_order_id = merc_shop_order_delivery.shop_order_id
                    INNER JOIN merc_customer_order_details ON merc_shop_order_delivery.delivery_id = merc_customer_order_details.details_id AND merc_shop_order_header.fg_id = merc_customer_order_details.fng_id
                    INNER JOIN merc_customer_order_header ON merc_customer_order_details.order_id = merc_customer_order_header.order_id
                    INNER JOIN cust_customer ON merc_customer_order_header.order_customer = cust_customer.customer_id
                    INNER JOIN style_creation ON merc_customer_order_header.order_style = style_creation.style_id
                    INNER JOIN item_master AS MAT ON merc_shop_order_detail.inventory_part_id = MAT.master_id
                    INNER JOIN item_master AS FNG ON merc_customer_order_details.fng_id = FNG.master_id
                    LEFT JOIN org_color AS STY_COLOUR ON FNG.color_id = STY_COLOUR.color_id
                    LEFT JOIN org_color AS MAT_COL ON MAT.color_id = MAT_COL.color_id
                    LEFT JOIN org_size AS MAT_SIZE ON MAT.size_id = MAT_SIZE.size_id
                    LEFT JOIN org_uom ON merc_shop_order_detail.purchase_uom = org_uom.uom_id
                    LEFT JOIN org_supplier ON merc_shop_order_detail.supplier = org_supplier.supplier_id
                    INNER JOIN org_location ON merc_customer_order_details.projection_location = org_location.loc_id
                    INNER JOIN org_origin_type ON merc_shop_order_detail.orign_type_id = org_origin_type.origin_type_id
                    INNER JOIN item_category ON MAT.category_id = item_category.category_id
                    INNER JOIN org_customer_divisions ON merc_customer_order_header.order_customer = org_customer_divisions.customer_id
                    AND merc_customer_order_header.order_division = org_customer_divisions.division_id
                    INNER JOIN item_subcategory ON MAT.subcategory_id = item_subcategory.subcategory_id
                    INNER JOIN bom_details ON merc_shop_order_detail.bom_detail_id = bom_details.bom_detail_id
                    left JOIN item_master AS SFG_IT ON bom_details.sfg_id = SFG_IT.master_id
                    left JOIN org_color AS SFG_COLOR ON SFG_IT.color_id = SFG_COLOR.color_id
                    WHERE
                    merc_customer_order_header.order_stage LIKE '%$stage%' AND
                    cust_customer.customer_name LIKE '%$customer_name%' AND
                    org_customer_divisions.division_id  LIKE '%".$customer_division."%' AND
                    $style_no_des
                    $category_des
										$sub_category_des
                    $supplier_des
                    $lot_des
                    merc_customer_order_details.projection_location LIKE '%".$location."%' AND
                    merc_shop_order_detail.po_status is null AND
                    merc_customer_order_details.type_modified is null
                    ");


                    $load_list['GFS'] = DB::select("SELECT
                    merc_shop_order_delivery.shop_order_id,
                    merc_shop_order_detail.shop_order_detail_id,
                    merc_shop_order_detail.bom_id,
                    merc_customer_order_details.details_id AS delivery_id,
                    merc_customer_order_header.order_stage,
                    cust_customer.customer_name,
                    style_creation.style_no,
                    merc_customer_order_header.order_code,
                    merc_customer_order_details.po_no,
                    merc_customer_order_details.pcd,
                    DATE_FORMAT(merc_customer_order_details.pcd, '%d-%b-%Y') AS pcd_01,
                    merc_customer_order_details.fng_id,
                    FNG.master_code AS fng_number,
                    MAT.master_code AS mat_code,
                    MAT.master_description,
                    STY_COLOUR.color_id,
                    STY_COLOUR.color_name,
                    MAT_COL.color_id AS material_color_id,
                    MAT_COL.color_name AS material_color,
                    MAT_SIZE.size_id,
                    MAT_SIZE.size_name,
                    org_uom.uom_id,
                    org_uom.uom_code,
                    org_supplier.supplier_id,
                    org_supplier.supplier_name,
                    merc_shop_order_detail.unit_price,
                    merc_shop_order_header.order_qty,
                    merc_shop_order_detail.gross_consumption,
                    ( ROUND((merc_shop_order_header.order_qty*merc_shop_order_detail.gross_consumption),4)) AS total_qty,
                    MAT.moq,
                    MAT.mcq,
                    org_location.loc_id,
                    org_location.loc_name,
                    (SELECT Count(EX.currency) AS ex_rate
                     FROM org_exchange_rate AS EX
                     WHERE EX.currency = org_supplier.currency ) AS ex_rate,
                    merc_customer_order_details.ship_mode,
                    org_origin_type.origin_type_id,
                    org_origin_type.origin_type,
                    item_category.category_id,
                    item_category.category_name,
                    IFNULL(merc_shop_order_detail.po_qty,0) AS req_qty,
                    round(IFNULL(merc_shop_order_detail.po_balance_qty,0),4) AS po_balance_qty,
                    merc_shop_order_detail.inventory_part_id,
                    ( SELECT GROUP_CONCAT( DISTINCT MPOD.po_no SEPARATOR ' | ' )AS po_nos
                      FROM merc_po_order_details AS MPOD WHERE
                      MPOD.shop_order_id = merc_shop_order_header.shop_order_id
                      AND MPOD.shop_order_detail_id = merc_shop_order_detail.shop_order_detail_id
                      ) AS po_nos,
                    merc_customer_order_details.type_created,
                    bom_details.sfg_id,
                    bom_details.sfg_code,
                    SFG_COLOR.color_name as SFG_COL
                    FROM
                    merc_customer_order_details
                    INNER JOIN merc_shop_order_delivery ON merc_customer_order_details.parent_line_id = merc_shop_order_delivery.delivery_id
                    INNER JOIN merc_shop_order_detail ON merc_shop_order_delivery.shop_order_id = merc_shop_order_detail.shop_order_id
                    INNER JOIN merc_shop_order_header ON merc_shop_order_delivery.shop_order_id = merc_shop_order_header.shop_order_id AND merc_customer_order_details.fng_id = merc_shop_order_header.fg_id
                    INNER JOIN merc_customer_order_header ON merc_customer_order_details.order_id = merc_customer_order_header.order_id
                    INNER JOIN cust_customer ON merc_customer_order_header.order_customer = cust_customer.customer_id
                    INNER JOIN style_creation ON merc_customer_order_header.order_style = style_creation.style_id
                    INNER JOIN item_master AS MAT ON merc_shop_order_detail.inventory_part_id = MAT.master_id
                    INNER JOIN item_master AS FNG ON merc_customer_order_details.fng_id = FNG.master_id
                    LEFT JOIN org_color AS STY_COLOUR ON FNG.color_id = STY_COLOUR.color_id
                    LEFT JOIN org_color AS MAT_COL ON MAT.color_id = MAT_COL.color_id
                    LEFT JOIN org_size AS MAT_SIZE ON MAT.size_id = MAT_SIZE.size_id
                    LEFT JOIN org_uom ON merc_shop_order_detail.purchase_uom = org_uom.uom_id
                    LEFT JOIN org_supplier ON merc_shop_order_detail.supplier = org_supplier.supplier_id
                    INNER JOIN org_location ON merc_customer_order_details.projection_location = org_location.loc_id
                    INNER JOIN org_origin_type ON merc_shop_order_detail.orign_type_id = org_origin_type.origin_type_id
                    INNER JOIN item_category ON MAT.category_id = item_category.category_id
                    INNER JOIN org_customer_divisions ON merc_customer_order_header.order_customer = org_customer_divisions.customer_id AND merc_customer_order_header.order_division = org_customer_divisions.division_id
                    INNER JOIN item_subcategory ON MAT.subcategory_id = item_subcategory.subcategory_id
                    INNER JOIN bom_details ON merc_shop_order_detail.bom_detail_id = bom_details.bom_detail_id
                    left JOIN item_master AS SFG_IT ON bom_details.sfg_id = SFG_IT.master_id
                    left JOIN org_color AS SFG_COLOR ON SFG_IT.color_id = SFG_COLOR.color_id
                    WHERE
                    merc_customer_order_header.order_stage LIKE '%$stage%' AND
                    cust_customer.customer_name LIKE '%$customer_name%' AND
                    org_customer_divisions.division_id  LIKE '%".$customer_division."%' AND
                    $style_no_des
                    $category_des
										$sub_category_des
                    $supplier_des
                    $lot_des
                    merc_customer_order_details.projection_location LIKE '%".$location."%' AND
                    merc_shop_order_detail.po_status IS null AND
                    merc_customer_order_details.type_created = 'GFS' AND
                    merc_customer_order_details.active_status = 'ACTIVE'
                    GROUP BY
                    merc_shop_order_delivery.shop_order_id,
										merc_shop_order_detail.shop_order_detail_id");

       //return $customer_list;
       return response([ 'data' => [
         'load_list' => $load_list,
         'count' => sizeof($load_list)
         ]
       ], Response::HTTP_CREATED );

  	}


    public function merge_save(Request $request){
      $lines = $request->lines;
      //dd($lines);

      for($r = 0 ; $r < sizeof($lines) ; $r++)
      {
        //bom_details
        $check_hold = ShopOrderDetail::where('shop_order_detail_id'  , '=', $lines[$r]['shop_order_detail_id'] )
        //->where('id'  , '=', $lines[$r]['bom_detail_id'] )
        ->where('po_status'  , '=', 'HOLD' )
        ->count();

        if($check_hold > 0){
          $line_id = $r+1;
          $err = 'Line '.$line_id.' Already used.';
          return response([ 'data' => ['status' => 'error','message' => $err]]);
        }

      }


      if($lines != null && sizeof($lines) >= 1){

        $max_no = PurchaseReqLines::max('merge_no');
        $max_no = $max_no + 1;

        for($x = 0 ; $x < sizeof($lines) ; $x++){


        $temp_line = new PurchaseReqLines();
        $temp_line->shop_order_id = $lines[$x]['shop_order_id'];
        $temp_line->shop_order_detail_id = $lines[$x]['shop_order_detail_id'];
        $temp_line->bom_id = $lines[$x]['bom_id'];
        //$temp_line->bom_detail_id= $lines[$x]['bom_detail_id'];//
        //$temp_line->mat_id= $lines[$x]['mat_id'];//
        $temp_line->merge_no = $max_no;
        $temp_line->item_code = $lines[$x]['inventory_part_id'];
        $temp_line->item_color = $lines[$x]['color_id'];
        $temp_line->mat_colour = $lines[$x]['material_color_id'];//
        $temp_line->item_size = $lines[$x]['size_id'];
        $temp_line->uom_id = $lines[$x]['uom_id'];
        $temp_line->supplier_id = $lines[$x]['supplier_id'];
        $temp_line->unit_price = $lines[$x]['unit_price'];
        $temp_line->total_qty = $lines[$x]['total_qty'];
        $temp_line->moq = $lines[$x]['moq'];
        $temp_line->mcq = $lines[$x]['mcq'];
        $temp_line->bal_order = $lines[$x]['bal_oder'];
        $temp_line->po_qty = $lines[$x]['req_qty'];
        $temp_line->status = '1';
        $temp_line->status_user = 'OPEN';
        $temp_line->bom_stage_id = $lines[$x]['order_stage'];
        $temp_line->ship_mode = $lines[$x]['ship_mode'];
        $temp_line->origin_type_id = $lines[$x]['origin_type_id'];
        $temp_line->delivery_loc = $lines[$x]['loc_id'];
        $temp_line->save();

        DB::table('merc_purchase_req_lines')
            ->join('merc_shop_order_detail', 'merc_shop_order_detail.shop_order_detail_id', '=', 'merc_purchase_req_lines.shop_order_detail_id')
            ->where('merge_no', $max_no)
            ->update(['po_status' => 'HOLD','po_con' => 'CREATE']);

        }

        return response([
          'data' => [
            'status' => 'success',
            'message' => 'Purchase order create sucessfully.',
            'merge_no' => $max_no
          ]
        ] , 200);

      }



    }


    public function load_reqline(Request $request)
  	{
      $prl_id = $request->prl_id;
      $user = auth()->user();

      $load_list = PurchaseReqLines::join('merc_shop_order_detail', 'merc_purchase_req_lines.shop_order_detail_id', '=', 'merc_shop_order_detail.shop_order_detail_id')
        ->join('merc_shop_order_header', 'merc_shop_order_detail.shop_order_id', '=', 'merc_shop_order_header.shop_order_id')
        ->join('item_master', 'item_master.master_id', '=', 'merc_shop_order_detail.inventory_part_id')
        ->join('item_category', 'item_category.category_id', '=', 'item_master.category_id')
        ->join('org_uom', 'org_uom.uom_id', '=', 'merc_purchase_req_lines.uom_id')
        ->leftjoin('org_size', 'org_size.size_id', '=', 'merc_purchase_req_lines.item_size')
        ->leftjoin('org_color', 'org_color.color_id', '=', 'merc_purchase_req_lines.mat_colour')
        ->join('merc_po_order_header', 'merc_po_order_header.prl_id', '=', 'merc_purchase_req_lines.merge_no')
        ->join('merc_customer_order_details', 'merc_customer_order_details.shop_order_id', '=', 'merc_shop_order_detail.shop_order_id')
        ->join('merc_customer_order_header', 'merc_customer_order_header.order_id', '=', 'merc_customer_order_details.order_id')
        ->leftjoin('org_uom AS purchase_u', 'purchase_u.uom_id', '=', 'merc_po_order_header.purchase_uom')
        ->leftjoin("conversion_factor",function($join){
          $join->on("conversion_factor.unit_code","=","org_uom.uom_code")
               ->on("conversion_factor.base_unit","=","purchase_u.uom_code");
              })
 	      ->select('purchase_u.uom_code as pur_uom_code','merc_po_order_header.purchase_uom','item_category.*','item_master.*',
        'merc_po_order_header.cur_value','org_uom.*','org_color.*','org_size.*','merc_purchase_req_lines.*',
        'merc_purchase_req_lines.bal_order as tra_qty','merc_shop_order_detail.unit_price as ori_unit_price',
        'merc_shop_order_detail.shop_order_detail_id','merc_shop_order_detail.shop_order_id','merc_shop_order_detail.purchase_price',
        'merc_customer_order_header.order_style AS style_id','merc_shop_order_header.order_qty','merc_shop_order_detail.gross_consumption',
        'conversion_factor.present_factor','conversion_factor.fractions','merc_purchase_req_lines.bal_order as original_req_qty')
        ->where('merge_no'  , '=', $prl_id )
        ->Where('merc_purchase_req_lines.created_by','=', $user->user_id)
        ->get();

       //echo $load_list;

       //print_r($load_list);

       return response([ 'data' => [
         'load_list' => $load_list,
         'prl_id' => $prl_id,
         'count' => sizeof($load_list)
         ]
       ], Response::HTTP_CREATED );

  	}











}
