<?php

namespace App\Http\Controllers\Store;
use App\Libraries\UniqueIdGenerator;
use App\Models\Store\MRNHeader;
use App\Models\Store\MRNDetail;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Org\Location\Cluster;
//use App\Models\mrn\MRN;
use App\Models\Finance\Transaction;
use App\Models\Store\StockTransaction;
use App\Models\Store\Stock;
use App\Models\Org\ConversionFactor;
use App\Models\Org\UOM;
use App\Models\Org\RequestType;
use App\Models\Org\Section;
use App\Models\Merchandising\ShopOrderHeader;
use App\Models\Merchandising\ShopOrderDetail;
use App\Models\Merchandising\StyleCreation;
class MrnController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $type = $request['type'];
        if($type == 'datatable')   {
          $data = $request->all();
          return response($this->datatable_search($data));
          }
          else if($type=="auto"){
            $search = $request->search;
            return response($this->autocomplete_search($search));
          }
        elseif($type == 'load-mrn'){
            $mrnId = $request['mrn'];
            $locId = $request['loc'];
            return $this->loadMrn($mrnId, $locId);

        }elseif ($type == 'mrn-select'){
            $soId = $request['so'];
            $active = $request->active;
            $fields = $request->fields;

            return $this->loadMrnList($soId, $fields);
        }

    }


    private function autocomplete_search($search)
     {
       $active=1;
       $mrn_list = MRNHeader::select('mrn_id','mrn_no')
       ->where([['mrn_no', 'like', '%' . $search . '%'],])
       ->where('status','=',$active)
       ->get();
       return $mrn_list;
     }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $header=$request->header;
      $details=$request->dataset;
      $locId=auth()->payload()['loc_id'];
      $unId = UniqueIdGenerator::generateUniqueId('MRN', auth()->payload()['loc_id']);

    for($i=0;$i<sizeof($details);$i++){
      $original_req_qty=$details[$i]['requested_qty'];
      /*if($details[$i]['uom_id']!=$details[$i]['inventory_uom_id']){
        //$storeUpdate->uom = $dataset[$i]['inventory_uom'];
        $_uom_unit_code=UOM::where('uom_id','=',$details[$i]['inventory_uom_id'])->pluck('uom_code');
        $_uom_base_unit_code=UOM::where('uom_id','=',$details[$i]['uom_id'])->pluck('uom_code');
        $ConversionFactor=ConversionFactor::select('*')
                                            ->where('unit_code','=',$_uom_unit_code[0])
                                            ->where('base_unit','=',$_uom_base_unit_code[0])
                                            ->first();
                                            // convert values according to the convertion rate
                                            $details[$i]['requested_qty'] =(double)($details[$i]['requested_qty']*$ConversionFactor->present_factor);


      }*/
    /*  if($details[$i]['uom_id']==$details[$i]['inventory_uom_id']){
        $details[$i]['requested_qty'] =$details[$i]['requested_qty'];
      }
*/
      $shopOrderDetail=ShopOrderDetail::find($details[$i]['shop_order_detail_id']);
      if($shopOrderDetail->asign_qty<$details[$i]['requested_qty']){
        return response(['data' => [
                'status' => 0,
                'message' => 'is Exceed the Shop Order Asign Qty ',
                'item_code' =>   $details[$i]['master_code'],
                'detailData'=>$details
            ]
        ], Response::HTTP_CREATED);

      }

    }
      $mrnHeader=new MRNHeader();
      $mrnHeader->mrn_no=$unId;
      $mrnHeader->style_id= $header['style_no']['style_id'];
      $mrnHeader->section_Id=$header['sec_name']['section_id'];
      $mrnHeader->line_no=$header['line_no'];
      $mrnHeader->request_type_id=$header['request_type']['request_type_id'];
      $mrnHeader->cut_qty=$header['cut_qty'];
      $mrnHeader->save();


      for($i=0;$i<sizeof($details);$i++){
      $mrndetails=new MRNDetail();
      $mrndetails->mrn_id=$mrnHeader->mrn_id;
      $mrndetails->item_id=$details[$i]['master_id'];
      $mrndetails->color_id=$details[$i]['color_id'];
      $mrndetails->size_id=$details[$i]['size_id'];
      $mrndetails->uom=$details[$i]['inventory_uom_id'];
      $mrndetails->gross_consumption=$details[$i]['gross_consumption'];
      $mrndetails->wastage=$details[$i]['wastage'];
      $mrndetails->order_qty=$details[$i]['order_qty'];
      $mrndetails->required_qty=$details[$i]['required_qty'];
//if requested qty uom is varid from po uom ,shop order asign qty should be changed according to the uom


      $mrndetails->requested_qty=(double)$details[$i]['requested_qty'];
      $mrndetails->total_qty=$details[$i]['total_qty'];
      $mrndetails->cust_order_detail_id=$details[$i]['details_id'];
      $mrndetails->shop_order_id=$details[$i]['shop_order_id'];
      $mrndetails->shop_order_detail_id=$details[$i]['shop_order_detail_id'];
      //find exact line of stock
      //$cus_po=$details[$i]['customer_po_id'];
      //$style_id=$mrnHeader->style_id;
      $item_code=$details[$i]['master_id'];
      //$size=$details[$i]['size'];
    //  $size=1;
    /*  $color=$details[$i]['color'];
      $main_store=$details[$i]['store'];
      $sub_store=$details[$i]['sub_store'];
      $bin=$details[$i]['bin'];
      if($details[$i]['size']==null){
        $size_serach=0;
      }
      else {
        $size_serach=$details[$i]['size'];
      }*/
      $shopOrderDetail=ShopOrderDetail::find($details[$i]['shop_order_detail_id']);
      $shopOrderDetail->mrn_qty=  $shopOrderDetail->mrn_qty+(double)$original_req_qty;
      $shopOrderDetail->balance_to_issue_qty=$shopOrderDetail->balance_to_issue_qty+(double)$original_req_qty;
      $shopOrderDetail->asign_qty=$shopOrderDetail->asign_qty-(double)$original_req_qty;
      $shopOrderDetail->save();
    /* $findStoreStockLine=DB::SELECT ("SELECT * FROM store_stock
                                    Where item_id=$item_code
                                       ");*/
      //$stock=Stock::find($findStoreStockLine[0]->id);

      if($details[$i]['uom_id']!=$details[$i]['inventory_uom_id']){
        //$storeUpdate->uom = $dataset[$i]['inventory_uom'];
        $_uom_unit_code=UOM::where('uom_id','=',$details[$i]['inventory_uom_id'])->pluck('uom_code');
        $_uom_base_unit_code=UOM::where('uom_id','=',$details[$i]['uom_id'])->pluck('uom_code');
        $ConversionFactor=ConversionFactor::select('*')
                                            ->where('unit_code','=',$_uom_unit_code[0])
                                            ->where('base_unit','=',$_uom_base_unit_code[0])
                                            ->first();
                                            // convert values according to the convertion rate
                                            $qty =(double)($details[$i]['requested_qty']*$ConversionFactor->present_factor);


      }
      if($details[$i]['uom_id']==$details[$i]['inventory_uom_id']){
        $qty =$details[$i]['requested_qty'];
      }




      //$stock->inv_qty=(double)$stock->inv_qty-(double)$qty;
      //$stock->save();
      $transaction = Transaction::where('trans_description', 'MRN')->first();
      //dd($transaction);
      $st = new StockTransaction;
      $st->status = 'PENDING';
      $st->doc_type = $transaction->trans_code;
      $st->doc_num = $mrndetails->mrn_id;
      $st->style_id =   $mrnHeader->style_id;
      //$st->main_store = $stock->store;
      //$st->sub_store = $stock->sub_store;
      $st->item_code =   $item_code;
      $st->size = $details[$i]['size_id'];
      $st->color = $details[$i]['color_id'];
      $st->shop_order_id=$details[$i]['shop_order_id'];
      $st->shop_order_detail_id=$details[$i]['shop_order_detail_id'];
      $st->uom = $details[$i]['inventory_uom_id'];
      $st->customer_po_id=$details[$i]['details_id'];
      $st->qty =  $qty;
      $st->location = auth()->payload()['loc_id'];
      //$st->bin = $stock->bin;
      $st->created_by = auth()->payload()['user_id'];
      $st->save();

      $mrndetails->save();


    }


            return response(['data' => [
                    'status' => 1,
                    'message2' => ' Saved Successfully',
                    'message1'=>'MRN No ',
                    'mrnId' => $mrnHeader->mrn_id,
                    'mrnNo'=>$mrnHeader->mrn_no,
                    'detailData'=>$mrndetails
                ]
            ], Response::HTTP_CREATED);

    }

    //get searched MRN Details for datatable plugin format
    private function datatable_search($data)
    {
      $start = $data['start'];
      $length = $data['length'];
      $draw = $data['draw'];
      $search = $data['search']['value'];
      $order = $data['order'][0];
      $order_column = $data['columns'][$order['column']]['data'];
      $order_type = $order['dir'];

    $mrn_list = MRNHeader::join('style_creation','store_mrn_header.style_id','=','style_creation.style_id')
      ->join('org_request_type','store_mrn_header.request_type_id','=','org_request_type.request_type_id')
      ->join('usr_login','store_mrn_header.updated_by','=','usr_login.user_id')
      ->select('store_mrn_header.*','style_creation.style_no','usr_login.user_name','org_request_type.request_type')
      ->where('style_creation.style_no'  , 'like', $search.'%' )
      //->orWhere('merc_customer_order_header.order_code'  , 'like', $search.'%' )
      ->orderBy($order_column, $order_type)
      ->offset($start)->limit($length)->get();

      $mrn_list_count = MRNHeader::join('style_creation','store_mrn_header.style_id','=','style_creation.style_id')
        ->join('org_request_type','store_mrn_header.request_type_id','=','org_request_type.request_type_id')
        ->join('usr_login','store_mrn_header.updated_by','=','usr_login.user_id')
        ->select('store_mrn_header.*','style_creation.style_no','usr_login.user_name','org_request_type.request_type')
        ->where('style_creation.style_no'  , 'like', $search.'%' )
        ->count();
        //dd($mrn_list_count);
      return [
          "draw" => $draw,
          "recordsTotal" => $mrn_list_count,
          "recordsFiltered" => $mrn_list_count,
          "data" => $mrn_list
      ];
    }



    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
      $locId=auth()->payload()['loc_id'];
      $status=1;
    /*  $mrndetails=MRNHeader::join('store_mrn_detail','store_mrn_header.mrn_id','=','store_mrn_detail.mrn_id')
      ->join('style_creation','store_mrn_header.style_id','=','style_creation.style_id')
      ->join('org_request_type','store_mrn_header.request_type_id','=','org_request_type.request_type_id')
      ->join('usr_login','store_mrn_header.updated_by','=','usr_login.user_id')
      ->join('item_master','store_mrn_detail.item_id','=','item_master.master_id')
      ->join('store_stock','item_master.master_id','=','store_stock.item_id')
      ->join('merc_shop_order_detail','store_mrn_detail.shop_order_detail_id','=','merc_shop_order_detail.shop_order_detail_id')
      ->join('merc_shop_order_header','merc_shop_order_detail.shop_order_id','=','merc_shop_order_header.shop_order_id')
      ->join('merc_shop_order_delivery','merc_shop_order_header.shop_order_id','=','merc_shop_order_delivery.shop_order_id')
      ->join('merc_customer_order_details','merc_shop_order_delivery.delivery_id','=','merc_customer_order_details.details_id')

      ->leftJoin('org_color','store_mrn_detail.color_id','=','org_color.color_id')
      ->leftJoin('org_size','store_mrn_detail.size_id','=','org_size.size_id')
      ->Join('org_uom as inv_uom','item_master.inventory_uom','=','inv_uom.uom_id')
      ->Join('org_uom','merc_shop_order_detail.purchase_uom','=','org_uom.uom_id')

      ->where('store_mrn_detail.mrn_id','=',$id)
      ->where('store_mrn_detail.status','=',$status)
      ->select('store_mrn_detail.mrn_detail_id','store_mrn_detail.mrn_id','store_mrn_detail.item_id','store_mrn_detail.color_id','store_mrn_detail.size_id','store_mrn_detail.uom','store_mrn_detail.gross_consumption','merc_shop_order_detail.wastage','store_mrn_detail.order_qty','store_mrn_detail.requested_qty','store_stock.total_qty','style_creation.style_no','usr_login.user_name','item_master.master_code','item_master.master_id','item_master.master_description','org_color.color_code','org_size.size_name','org_uom.uom_code','org_uom.uom_id','store_mrn_detail.*','merc_shop_order_detail.asign_qty','merc_shop_order_detail.gross_consumption','merc_shop_order_detail.balance_to_issue_qty','merc_shop_order_detail.required_qty','merc_shop_order_detail.balance_to_issue_qty','inv_uom.uom_code as inventory_uom','inv_uom.uom_id as inventory_uom_id','store_mrn_detail.requested_qty as pre_qty','merc_customer_order_details.details_id'
      )
      ->get();*/

      $mrndetails=DB::SELECT ("SELECT store_mrn_detail.mrn_detail_id,
        store_mrn_detail.mrn_id,
        store_mrn_detail.item_id,
        store_mrn_detail.color_id,
        store_mrn_detail.size_id,
        store_mrn_detail.uom,
        store_mrn_detail.gross_consumption,merc_shop_order_detail.wastage,store_mrn_detail.order_qty,store_mrn_detail.requested_qty,style_creation.style_no,usr_login.user_name,item_master.master_code,item_master.master_id,item_master.master_description,org_color.color_code,org_color.color_name,org_size.size_name,org_uom.uom_code,org_uom.uom_id,store_mrn_detail.*,merc_shop_order_detail.asign_qty,merc_shop_order_detail.gross_consumption,merc_shop_order_detail.balance_to_issue_qty,merc_shop_order_detail.balance_to_issue_qty,inv_uom.uom_code as inventory_uom,inv_uom.uom_id as inventory_uom_id,store_mrn_detail.requested_qty as pre_qty,merc_customer_order_details.details_id,

        (select
          IFNULL(SUM(STK_BALANCE.qty),0)
          from store_stock as STK_BALANCE
          where STK_BALANCE.item_id=item_master.master_id
          AND STK_BALANCE.location=$locId
          GROUP By(item_master.master_id)
        ) as total_qty,
        (select (merc_shop_order_header.order_qty*merc_shop_order_detail.gross_consumption) as required_qty
        from merc_shop_order_detail as SOD2
        INNER JOIN merc_shop_order_header ON SOD2.shop_order_id=merc_shop_order_header.shop_order_id
        where SOD2.shop_order_detail_id=merc_shop_order_detail.shop_order_detail_id
        )as required_qty

      FROM
      store_mrn_header
      INNER JOIN  store_mrn_detail ON store_mrn_header.mrn_id=store_mrn_detail.mrn_id
      INNER JOIN style_creation ON store_mrn_header.style_id=style_creation.style_id
      INNER JOIN org_request_type ON store_mrn_header.request_type_id=org_request_type.request_type_id
      INNER JOIN usr_login ON store_mrn_header.updated_by=usr_login.user_id
      INNER JOIN item_master ON store_mrn_detail.item_id=item_master.master_id
      INNER JOIN store_stock oN item_master.master_id=store_stock.item_id
      INNER JOIN merc_shop_order_detail ON store_mrn_detail.shop_order_detail_id=merc_shop_order_detail.shop_order_detail_id
      INNER JOIN merc_shop_order_header ON merc_shop_order_detail.shop_order_id=merc_shop_order_header.shop_order_id
      INNER JOIN merc_shop_order_delivery ON merc_shop_order_header.shop_order_id=merc_shop_order_delivery.shop_order_id
      INNER JOIN merc_customer_order_details ON merc_shop_order_delivery.delivery_id=merc_customer_order_details.details_id
      LEFT JOIN org_color on store_mrn_detail.color_id=org_color.color_id
      LEFT JOIN org_size ON store_mrn_detail.size_id=org_size.size_id
      JOIN org_uom as inv_uom ON item_master.inventory_uom=inv_uom.uom_id
      inner Join org_uom ON  merc_shop_order_detail.purchase_uom=org_uom.uom_id
      WHERE store_mrn_detail.mrn_id=$id
      AND store_mrn_detail.status=1
      AND merc_shop_order_detail.asign_qty>0/*(
        SELECT
          IFNULL(SUM(merc_shop_order_detail.asign_qty),0)
        from merc_shop_order_detail as SOD
        where SOD.shop_order_detail_id=merc_shop_order_detail.shop_order_detail_id
      )*/
     GROUP By(merc_shop_order_detail.shop_order_detail_id)
      ");

    //dd($mrndetails);

      $mrnHeader= MRNHeader::join('store_mrn_detail','store_mrn_header.mrn_id','=','store_mrn_detail.mrn_id')
        ->join('style_creation','store_mrn_header.style_id','=','style_creation.style_id')
        ->join('org_request_type','store_mrn_header.request_type_id','=','org_request_type.request_type_id')
        ->join('usr_login','store_mrn_header.updated_by','=','usr_login.user_id')
        ->join('org_section','store_mrn_header.section_id','=','org_section.section_id')
        ->where('store_mrn_header.mrn_id','=',$id)
        ->where('store_mrn_header.status','=',$status)
        ->select('store_mrn_header.*','style_creation.style_no','usr_login.user_name','org_request_type.request_type','org_section.section_name as sec_name')
        ->first();
      //  dd($mrnHeader['style_id']);
        $style=StyleCreation::find($mrnHeader['style_id']);
        $reqestType=RequestType::find($mrnHeader['request_type_id']);
        $sction=Section::find($mrnHeader['section_id']);

        if($mrndetails == null)
          throw new ModelNotFoundException("Requested mrn details not found", 1);
        else
          return response([ 'data'  => ['dataDetails'=>$mrndetails,
                                      'dataHeader'=>$mrnHeader,
                                      'style'=>$style,
                                      'requestType'=>$reqestType,
                                      'section'=>$sction

                                      ]
                              ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
      //dd($request->dataset);
      $header=$request->header;
      $details=$request->dataset;
      $locId=auth()->payload()['loc_id'];
      for($i=0;$i<sizeof($details);$i++){
        /*if(empty($details[$i]['mrn_detail_id'])==false){
          $original_req_qty=(double)$details[$i]['pre_qty']-(double)$details[$i]['requested_qty'];
          //dd($original_req_qty);
        }*
        $original_req_qty=$details[$i]['requested_qty'];
          /*if ($details[$i]['uom_id']!=$details[$i]['inventory_uom_id']){
          //$storeUpdate->uom = $dataset[$i]['inventory_uom'];
          $_uom_unit_code=UOM::where('uom_id','=',$details[$i]['inventory_uom_id'])->pluck('uom_code');
          $_uom_base_unit_code=UOM::where('uom_id','=',$details[$i]['uom_id'])->pluck('uom_code');
          $ConversionFactor=ConversionFactor::select('*')
                                              ->where('unit_code','=',$_uom_unit_code[0])
                                              ->where('base_unit','=',$_uom_base_unit_code[0])
                                              ->first();
                                              // convert values according to the convertion rate
                                              if(empty($details[$i]['mrn_detail_id'])==false){
                                                $original_req_qty=(double)$details[$i]['pre_qty']-(double)$details[$i]['requested_qty'];
                                                $details[$i]['requested_qty'] =((double)$details[$i]['pre_qty']-(double)($details[$i]['requested_qty'])*$ConversionFactor->present_factor);
                                                }
                                              $details[$i]['requested_qty'] =(double)($details[$i]['requested_qty']*$ConversionFactor->present_factor);



        }/*/

        $shopOrderDetail=ShopOrderDetail::find($details[$i]['shop_order_detail_id']);
        //dd($shopOrderDetail);
        if(empty($details[$i]['mrn_detail_id'])==false){
          $qty =(double)($details[$i]['pre_qty'])-(double)($details[$i]['requested_qty']);
        }
        if(empty($details[$i]['mrn_detail_id'])==true){
          $qty =$details[$i]['requested_qty'];
        }
        if($shopOrderDetail->asign_qty<$qty ){
          return response(['data' => [
                  'status' => 0,
                  'message' => 'is Exceed the Shop Order Asign Qty ',
                  'item_code' =>   $details[$i]['master_code'],
                  'detailData'=>$details
              ]
          ], Response::HTTP_CREATED);

        }
        $mrnHeader=MRNHeader::find($id);
        //dd($mrnHeader);
        $mrnHeader->section_id=$header['sec_name']['section_id'];
        $mrnHeader->line_no=$header['line_no'];
        $mrnHeader->request_type_id=$header['request_type']['request_type_id'];
        $mrnHeader->save();



              for($i=0;$i<sizeof($details);$i++){
                if(empty($details[$i]['mrn_detail_id'])==false){
                    //dd("dada");
                    $djd=$details[$i]['mrn_detail_id'];
                    $mrndetails= MRNDetail::find($djd);
                    //dd($mrndetails);
                    $mrndetails->requested_qty=(double)$details[$i]['requested_qty']-(double)$details[$i]['pre_qty'];
                    $mrndetails->total_qty=$details[$i]['total_qty'];
                    $mrndetails->save();
                    //dd($mrndetails);
                    $shopOrderDetail=ShopOrderDetail::find($details[$i]['shop_order_detail_id']);
                    $shopOrderDetail->mrn_qty=$shopOrderDetail->mrn_qty+$mrndetails->requested_qty;
                    $shopOrderDetail->balance_to_issue_qty=$shopOrderDetail->balance_to_issue_qty+$mrndetails->requested_qty;
                    $shopOrderDetail->asign_qty=$shopOrderDetail->asign_qty-$mrndetails->requested_qty;
                    $shopOrderDetail->save();

                    $item_code=$details[$i]['master_id'];
                    /*$findStoreStockLine=DB::SELECT ("SELECT * FROM store_stock
                                                  Where item_id=$item_code
                                                     ");*/
                  //$stock=Stock::find($findStoreStockLine[0]->id);

                    if($details[$i]['uom_id']!=$details[$i]['inventory_uom_id']){
                      //$storeUpdate->uom = $dataset[$i]['inventory_uom'];
                      $_uom_unit_code=UOM::where('uom_id','=',$details[$i]['inventory_uom_id'])->pluck('uom_code');
                      $_uom_base_unit_code=UOM::where('uom_id','=',$details[$i]['uom_id'])->pluck('uom_code');
                      $ConversionFactor=ConversionFactor::select('*')
                                                          ->where('unit_code','=',$_uom_unit_code[0])
                                                          ->where('base_unit','=',$_uom_base_unit_code[0])
                                                          ->first();
                                                          // convert values according to the convertion rate
                                                          $qty =(double)($mrndetails->requested_qty*$ConversionFactor->present_factor);


                    }
                    if($details[$i]['uom_id']==$details[$i]['inventory_uom_id']){
                      $qty =$mrndetails->requested_qty;
                    }


                  //  $stock->inv_qty=(double)$stock->inv_qty-$qty;
                    //$stock->save();
                    $transaction = Transaction::where('trans_description', 'MRN')->first();
                    //dd($transaction);
                    $st = new StockTransaction;
                    $st->status = 'PENDING';
                    $st->doc_type = $transaction->trans_code;
                    $st->doc_num = $mrndetails->mrn_id;
                    $st->style_id =   $mrnHeader->style_id;
                    //$st->main_store = $stock->store;
                    //$st->sub_store = $stock->sub_store;
                    $st->item_code = $item_code;
                    $st->size = $details[$i]['size_id'];
                    $st->color = $details[$i]['color_id'];
                    $st->shop_order_id=$details[$i]['shop_order_id'];
                    $st->shop_order_detail_id=$details[$i]['shop_order_detail_id'];
                    $st->uom = $details[$i]['inventory_uom_id'];
                    $st->customer_po_id=$details[$i]['details_id'];
                    $st->qty = $qty;
                    $st->location = auth()->payload()['loc_id'];
                    //$st->bin = $stock->bin;
                    $st->created_by = auth()->payload()['user_id'];
                    $st->save();


                }
             else if(empty($details[$i]['mrn_detail_id'])==true){
               $mrndetails=new MRNDetail();
               $mrndetails->mrn_id=$mrnHeader->mrn_id;
               $mrndetails->item_id=$details[$i]['master_id'];
               $mrndetails->color_id=$details[$i]['color_id'];
               $mrndetails->size_id=$details[$i]['size_id'];
               $mrndetails->uom=$details[$i]['inventory_uom_id'];
               $mrndetails->gross_consumption=$details[$i]['gross_consumption'];
               $mrndetails->wastage=$details[$i]['wastage'];
               $mrndetails->order_qty=$details[$i]['order_qty'];
               $mrndetails->required_qty=$details[$i]['required_qty'];
               $mrndetails->cust_order_detail_id=$details[$i]['details_id'];
         //if requested qty uom is varid from po uom ,shop order asign qty should be changed according to the uom


               $mrndetails->requested_qty=(double)$details[$i]['requested_qty'];
               $mrndetails->total_qty=$details[$i]['total_qty'];
               //$mrndetails->bal_qty=$details[$i]['bal_qty'];
               $mrndetails->shop_order_id=$details[$i]['shop_order_id'];
               $mrndetails->shop_order_detail_id=$details[$i]['shop_order_detail_id'];
               //find exact line of stock
               //$cus_po=$details[$i]['customer_po_id'];
               //$style_id=$mrnHeader->style_id;
               $item_code=$details[$i]['master_id'];
               //$size=$details[$i]['size'];
             //  $size=1;
             /*  $color=$details[$i]['color'];
               $main_store=$details[$i]['store'];
               $sub_store=$details[$i]['sub_store'];
               $bin=$details[$i]['bin'];
               if($details[$i]['size']==null){
                 $size_serach=0;
               }
               else {
                 $size_serach=$details[$i]['size'];
               }*/
               $shopOrderDetail=ShopOrderDetail::find($details[$i]['shop_order_detail_id']);
               $shopOrderDetail->mrn_qty=  $shopOrderDetail->mrn_qty+(double)$details[$i]['requested_qty'];
               $shopOrderDetail->balance_to_issue_qty=$shopOrderDetail->balance_to_issue_qty+(double)$details[$i]['requested_qty'];
               $shopOrderDetail->asign_qty=$shopOrderDetail->asign_qty-(double)$details[$i]['requested_qty'];
               $shopOrderDetail->save();
               /*$findStoreStockLine=DB::SELECT ("SELECT * FROM store_stock
                                             Where item_id=$item_code
                                                ");*/
              // $stock=Stock::find($findStoreStockLine[0]->id);

               if($details[$i]['uom_id']!=$details[$i]['inventory_uom_id']){
                 //$storeUpdate->uom = $dataset[$i]['inventory_uom'];
                 $_uom_unit_code=UOM::where('uom_id','=',$details[$i]['inventory_uom_id'])->pluck('uom_code');
                 $_uom_base_unit_code=UOM::where('uom_id','=',$details[$i]['uom_id'])->pluck('uom_code');
                 $ConversionFactor=ConversionFactor::select('*')
                                                     ->where('unit_code','=',$_uom_unit_code[0])
                                                     ->where('base_unit','=',$_uom_base_unit_code[0])
                                                     ->first();
                                                     // convert values according to the convertion rate
                                                     $qty =(double)($details[$i]['requested_qty']*$ConversionFactor->present_factor);


               }
               if($details[$i]['uom_id']==$details[$i]['inventory_uom_id']){
                 $qty =$details[$i]['requested_qty'];
               }




              // $stock->inv_qty=(double)$stock->inv_qty-(double)$qty;
               //$stock->save();
               $transaction = Transaction::where('trans_description', 'MRN')->first();
               //dd($transaction);
               $st = new StockTransaction;
               $st->status = 'PENDING';
               $st->doc_type = $transaction->trans_code;
               $st->doc_num = $mrndetails->mrn_id;
               $st->style_id =   $mrnHeader->style_id;
               //$st->main_store = $stock->store;
               //$st->sub_store = $stock->sub_store;
               $st->item_code = $details[$i]['master_id'];
               $st->size = $details[$i]['size_id'];
               $st->color = $details[$i]['color_id'];
               $st->shop_order_id=$details[$i]['shop_order_id'];
               $st->shop_order_detail_id=$details[$i]['shop_order_detail_id'];
               $st->uom = $details[$i]['inventory_uom_id'];
               $st->customer_po_id=$details[$i]['details_id'];
               $st->qty =  $qty;
               $st->location = auth()->payload()['loc_id'];
               //$st->bin = $stock->bin;
               $st->created_by = auth()->payload()['user_id'];
               $st->save();

               $mrndetails->save();



        }
            }





      }

      return response(['data' => [
              'status' => 1,
              'message' => 'MRN Saved Updated sucessfully.',
              'grnId' => $mrnHeader->mrn_id,
              'detailData'=>$mrndetails
          ]
      ], Response::HTTP_CREATED);


        //dd($request);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      //dd($id);
      $mrndetails=MRNHeader::join('store_mrn_detail','store_mrn_header.mrn_id','=','store_mrn_detail.mrn_id')
                            ->join('merc_shop_order_detail','store_mrn_detail.shop_order_detail_id','=','merc_shop_order_detail.shop_order_detail_id')
                            ->join('merc_shop_order_header','merc_shop_order_detail.shop_order_id','=','merc_shop_order_header.shop_order_id')
                            ->join('merc_po_order_details','merc_shop_order_detail.shop_order_detail_id','=','merc_po_order_details.shop_order_detail_id')
                            ->join('item_master','merc_po_order_details.item_code','=','item_master.master_id')
                            ->select('store_mrn_header.*')
                            ->where('store_mrn_header.mrn_id','=',$id)
                            ->get();

    $mrnHeader=MRNHeader::find($id);
    //dd($mrnHeader);
    $mrnHeader->status=0;
    $mrnHeader->save();
    $findmrnDetails=MRNDetail::where('mrn_id','=',$id)->get();
  //dd($findmrnDetails[0]['status']);
    for($i=0;$i<sizeof($findmrnDetails);$i++){

      $findmrnDetails[$i]['status']=0;
      $qty=$findmrnDetails[$i]['requested_qty'];
      $findShopOrderline=ShopOrderDetail::find($findmrnDetails[$i]['shop_order_detail_id']);
      $findShopOrderline->asign_qty=$findShopOrderline->asign_qty+$qty;
      $findShopOrderline->mrn_qty=$findShopOrderline->mrn_qty-$qty;
      $findShopOrderline->balance_to_issue_qty=  $findShopOrderline->balance_to_issue_qty-$qty;

      $transaction = Transaction::where('trans_description', 'MRN')->first();
      //dd($transaction);
      $st = new StockTransaction;
      $st->status = 'PENDING';
      $st->doc_type = $transaction->trans_code;
      $st->doc_num = $findmrnDetails[$i]['mrn_id'];
      $st->style_id =  $mrnHeader->style_id;
      //$st->main_store = $stock->store;
      //$st->sub_store = $stock->sub_store;
      $st->item_code = $findmrnDetails[$i]['item_id'];
      $st->size = $findmrnDetails[$i]['size_id'];
      $st->color = $findmrnDetails[$i]['color_id'];
      $st->shop_order_id=$findmrnDetails[$i]['shop_order_id'];
      $st->shop_order_detail_id=$findmrnDetails[$i]['shop_order_detail_id'];
      $st->uom = $findmrnDetails[$i]['uom'];
      $st->customer_po_id=$findmrnDetails[$i]['cust_order_detail_id'];
      $st->qty =  $qty;
      $st->location = auth()->payload()['loc_id'];
      //$st->bin = $stock->bin;
      $st->created_by = auth()->payload()['user_id'];
      $st->save();
      //dd($st);
      $findShopOrderline->save();
      //dd($findShopOrderline);
        $findmrnDetails[$i]->save();
        }
        //dd($findmrnDetails);


        return response([
          'data' => [
            'message' => 'MRN deactivated successfully.',
            'status'=>1
          ]
        ] );
    }

    public function loadMrnList($soId, $fields){

        $mrnList = MRNHeader::getMRNList($soId);

        return response([
            'data' => $mrnList
        ]);

    }

    public function loadMrn($mrnId, $locId){

    }

    public function filterData(Request $request){
    $styleNo=$request['style_id'] ;
    $item_code_filter=$request['item_code_filter'];
    $shop_order_filter=$request['shop_order_filter'];
    $customer_po_filter=$request['customer_po_filter'];
    $locId=auth()->payload()['loc_id'];

          $data=DB::SELECT("SELECT merc_po_order_details.*,merc_shop_order_detail.asign_qty,
            item_master.master_description,
            item_master.master_code,
            item_master.master_id,for_inv_uom.uom_code as inventory_uom,for_inv_uom.uom_id as inventory_uom_id,for_po_uom.uom_code,for_po_uom.uom_id,merc_shop_order_detail.gross_consumption,merc_shop_order_detail.wastage,
            merc_customer_order_details.order_qty,merc_shop_order_detail.shop_order_detail_id,merc_shop_order_detail.asign_qty,merc_shop_order_detail.balance_to_issue_qty,merc_shop_order_header.shop_order_id,merc_customer_order_details.details_id,org_color.color_id,org_color.color_name,org_size.size_id,

      ( select
        IFNULL(SUM(SOD.balance_to_issue_qty),0)
       FROM merc_shop_order_detail as SOD
        where
        SOD.shop_order_detail_id=merc_shop_order_detail.shop_order_detail_id
        GROUP BY(SOD.shop_order_detail_id)
      ) as balance_to_issue_qty,

      (select (merc_shop_order_header.order_qty*merc_shop_order_detail.gross_consumption) as required_qty
      from merc_shop_order_detail as SOD2
      INNER JOIN merc_shop_order_header ON SOD2.shop_order_id=merc_shop_order_header.shop_order_id
      where SOD2.shop_order_detail_id=merc_shop_order_detail.shop_order_detail_id
      )as required_qty,
      (select
        IFNULL(SUM(STK_BALANCE.qty),0)
        from store_stock as STK_BALANCE
        where STK_BALANCE.item_id=item_master.master_id
        AND STK_BALANCE.location=$locId
        GROUP By(item_master.master_id)
      ) as total_qty

    FROM

    merc_po_order_header
    INNER JOIN merc_po_order_details ON merc_po_order_header.po_number = merc_po_order_details.po_no
    INNER JOIN merc_shop_order_detail on merc_po_order_details.shop_order_detail_id=merc_shop_order_detail.shop_order_detail_id
    INNER JOIN merc_shop_order_header on  merc_shop_order_detail.shop_order_id=merc_shop_order_header.shop_order_id
    INNER JOIN merc_shop_order_delivery on merc_shop_order_header.shop_order_id=merc_shop_order_delivery.shop_order_id
    INNER JOIN merc_customer_order_details ON merc_shop_order_delivery.delivery_id = merc_customer_order_details.details_id
    INNER JOIN merc_customer_order_header ON merc_customer_order_details.order_id = merc_customer_order_header.order_id
    INNER JOIN style_creation on merc_customer_order_header.order_style=style_creation.style_id
    INNER JOIN item_master on merc_shop_order_detail.inventory_part_id=item_master.master_id
    INNER JOIN org_uom as for_inv_uom on item_master.inventory_uom=for_inv_uom.uom_id
    INNER JOIN org_uom as for_po_uom on merc_shop_order_detail.purchase_uom=for_po_uom.uom_id
    LEFT JOIN org_color on merc_po_order_details.colour=org_color.color_id
    LEFT JOIN org_size on merc_po_order_details.size=org_size.size_id
    INNER JOIN store_stock on item_master.master_id=store_stock.item_id
    where style_creation.style_id=$styleNo
    AND  store_stock.location=$locId
    AND merc_shop_order_detail.asign_qty>0
    AND merc_shop_order_header.shop_order_id like '%".$shop_order_filter."%'
    AND merc_customer_order_header.order_id like '%".$customer_po_filter."%'
    AND item_master.master_code like '%".$item_code_filter."%'
    GROUP By(merc_shop_order_detail.shop_order_detail_id)");

    return response([
        'data' => $data
    ]);



    }

    public function loadDetails(Request $request ){
      //$soNo=$request->so_no;
      //$soDetailsID=$request->so_detail_id;
      //$custoMerPo=$request->customer_po;
      $styleNo=$request->style_id;
      $locId=auth()->payload()['loc_id'];
      //dd($locId);

      $data=DB::SELECT("SELECT merc_po_order_details.*,merc_shop_order_detail.asign_qty,item_master.master_description,item_master.master_code,item_master.master_id,for_inv_uom.uom_code as inventory_uom,for_inv_uom.uom_id as inventory_uom_id,for_po_uom.uom_code,for_po_uom.uom_id,merc_shop_order_detail.gross_consumption,merc_shop_order_detail.wastage,
        merc_customer_order_details.order_qty,merc_shop_order_detail.shop_order_detail_id,merc_shop_order_detail.asign_qty,merc_shop_order_detail.balance_to_issue_qty,merc_shop_order_header.shop_order_id,merc_customer_order_details.details_id,org_color.color_id,org_color.color_name,org_size.size_id,org_size.size_name,
     ( select
  IFNULL(SUM(SOD.balance_to_issue_qty),0)
 FROM merc_shop_order_detail as SOD
  where
  SOD.shop_order_detail_id=merc_shop_order_detail.shop_order_detail_id
  GROUP BY(SOD.shop_order_detail_id)
) as balance_to_issue_qty,

(select
  IFNULL(SUM(STK_BALANCE.qty),0)
  from store_stock as STK_BALANCE
  where STK_BALANCE.item_id=item_master.master_id
  AND STK_BALANCE.location=$locId
  GROUP By(item_master.master_id)
) as total_qty,

(select (merc_shop_order_header.order_qty*merc_shop_order_detail.gross_consumption) as required_qty
from merc_shop_order_detail as SOD2
INNER JOIN merc_shop_order_header ON SOD2.shop_order_id=merc_shop_order_header.shop_order_id
where SOD2.shop_order_detail_id=merc_shop_order_detail.shop_order_detail_id
)as required_qty
FROM

merc_po_order_header
INNER JOIN merc_po_order_details ON merc_po_order_header.po_number = merc_po_order_details.po_no
INNER JOIN merc_shop_order_detail on merc_po_order_details.shop_order_detail_id=merc_shop_order_detail.shop_order_detail_id
INNER JOIN merc_shop_order_header on  merc_shop_order_detail.shop_order_id=merc_shop_order_header.shop_order_id
INNER JOIN merc_shop_order_delivery on merc_shop_order_header.shop_order_id=merc_shop_order_delivery.shop_order_id
INNER JOIN merc_customer_order_details ON merc_shop_order_delivery.delivery_id = merc_customer_order_details.details_id
INNER JOIN merc_customer_order_header ON merc_customer_order_details.order_id = merc_customer_order_header.order_id
INNER JOIN style_creation on merc_customer_order_header.order_style=style_creation.style_id
INNER JOIN item_master on merc_shop_order_detail.inventory_part_id=item_master.master_id
INNER JOIN org_uom as for_inv_uom on item_master.inventory_uom=for_inv_uom.uom_id
INNER JOIN org_uom as for_po_uom on merc_shop_order_detail.purchase_uom=for_po_uom.uom_id
INNER JOIN store_stock on item_master.master_id=store_stock.item_id
LEFT JOIN org_color on merc_po_order_details.colour=org_color.color_id
LEFT JOIN org_size on merc_po_order_details.size=org_size.size_id

where store_stock.location=$locId
AND style_creation.style_id=$styleNo
AND merc_shop_order_detail.asign_qty>0
GROUP By(merc_shop_order_detail.shop_order_detail_id)
");


//dd($deta);
return response(['data' => $data]);
    }
}
