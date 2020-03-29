<?php

namespace App\Http\Controllers\Manufacturing;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Manufacturing\ManProdOrderHeader;
use App\Models\Manufacturing\ManProdOrderDetails;
use App\Models\Merchandising\CustomerOrderDetails;
use App\Models\Merchandising\CustomerOrder;
use Illuminate\Support\Facades\DB;

class ProdOrderController extends Controller
{

    /**
 * Display a listing of the resource.
 *
 * @return \Illuminate\Http\Response
 */
    public function index(Request $request) {
        $type = $request->type;

        if ($type == 'auto') {
            $search = $request->search;
            return response($this->getStyleList($search));
        }elseif ($type == 'load_frontend_data'){
            $this->load_frontend_data($request);
        }elseif ($type == 'load_size_data'){
        $this->load_size_data($request);
        }

    }
    private function getStyleList($search) {
        return \App\Models\Merchandising\StyleCreation::select('style_id', 'style_no')
            ->where([['style_no', 'like', '%' . $search . '%'],['status', '=',  1 ]])->get();
    }

    public  function load_frontend_data($request){
        $styleData = \App\Models\Merchandising\StyleCreation::find($request->style_id);



        $getCustomerOrderData=DB::select('SELECT
merc_customer_order_details.details_id,
merc_customer_order_details.pcd,
merc_customer_order_details.rm_in_date,
merc_customer_order_details.po_no,
merc_customer_order_details.planned_delivery_date,
merc_customer_order_details.revised_delivery_date,
merc_customer_order_details.fob,
merc_customer_order_details.projection_location,
merc_customer_order_details.order_qty,
merc_customer_order_details.excess_presentage,
merc_customer_order_details.planned_qty,
merc_customer_order_details.ship_mode,
org_country.country_description,
org_color.color_name,
org_location.loc_name,
\'<a  style="min-height: 12px !important;padding: 1px 10px;font-size: 6px; line-height: 1; border-radius: 2px;margin: 1px;"  class="btn bg-success-400 btn-rounded  btn-icon btn-xs-new"><i class="letter-icon">size</i> </a>\' AS size,
\'<a  style="min-height: 12px !important;padding: 1px 10px;font-size: 6px; line-height: 1; border-radius: 2px;margin: 1px;"  class="btn bg-primary-400 btn-rounded  btn-icon btn-xs-new"><i class="letter-icon">size</i> </a>\' AS AddToOrder
FROM
merc_customer_order_header
INNER JOIN merc_customer_order_details ON merc_customer_order_header.order_id = merc_customer_order_details.order_id
INNER JOIN org_country ON merc_customer_order_details.country = org_country.country_id
INNER JOIN org_color ON merc_customer_order_details.style_color = org_color.color_id
INNER JOIN org_location ON org_location.loc_id = merc_customer_order_details.projection_location
WHERE merc_customer_order_header.order_style ='.$request->style_id.' AND
merc_customer_order_details.delivery_status !="CONFIRMED" AND
 merc_customer_order_details.version_no = (select MAX(b.version_no) from merc_customer_order_details b where b.order_id = merc_customer_order_details.order_id and merc_customer_order_details.line_no=b.line_no)
');

        $getProdOrderData=DB::select('SELECT
merc_customer_order_header.order_style,
man_prod_order_details.cus_order_details_id,
man_prod_order_header.`status`
FROM
man_prod_order_details
INNER JOIN man_prod_order_header ON man_prod_order_details.prod_id = man_prod_order_header.prod_id
INNER JOIN merc_customer_order_header ON merc_customer_order_header.order_id = man_prod_order_header.cus_order_id
INNER JOIN merc_customer_order_details ON merc_customer_order_details.details_id = man_prod_order_details.cus_order_details_id
WHERE
merc_customer_order_header.order_style ='.$request->style_id.' AND man_prod_order_header.status =1
');
//        echo '<pre>',print_r($getCustomerOrderData,1),'</pre>';exit;
//foreach ($getCustomerOrderData AS $CustomerOrderData ){
//    foreach ($getProdOrderData AS $ProdOrderData ){
//
//
//    }

//}


        print_r(json_encode(array('image'=>$styleData->image,'data'=>$getCustomerOrderData)));exit;
    }


  public  function load_size_data($request){

      $getSizeData=DB::select('SELECT 
org_size.size_name,
merc_customer_order_details.order_id,
merc_customer_order_size.order_qty,
merc_customer_order_size.version_no,
org_size.size_id
FROM
merc_customer_order_size
INNER JOIN merc_customer_order_details ON merc_customer_order_details.details_id = merc_customer_order_size.details_id
INNER JOIN org_size ON org_size.size_id = merc_customer_order_size.size_id
WHERE 
merc_customer_order_details.details_id='.$request->details_id.'
ORDER BY merc_customer_order_size.version_no DESC');

        $size=array();$size_name='';$i=0;
      foreach ($getSizeData AS $SizeData){
          if($SizeData->size_name != $size_name ){

              $size[$i]['name']=$SizeData->size_name;
              $size[$i]['qty']=$SizeData->order_qty;
              $size_name=$SizeData->size_name;
              $i++;

          }


      }
      print_r(json_encode(array('data'=>$size)));exit;
  }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $detail = CustomerOrderDetails::find($request[0][0]);
        $prodOrderHeader = new ManProdOrderHeader();
        $prodOrderHeader->cus_order_id=$detail['order_id'];
        $prodOrderHeader->save();


        foreach ($request[0] AS $id){
            $detail = CustomerOrderDetails::find($id);
            $prodOrderDetails = new ManProdOrderDetails();
            $prodOrderDetails->prod_id=$prodOrderHeader->prod_id;
            $prodOrderDetails->cus_order_details_id=$detail['details_id'];
            $prodOrderDetails->save();
        }

    }
}
