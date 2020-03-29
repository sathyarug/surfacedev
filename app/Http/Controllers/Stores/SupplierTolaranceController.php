<?php
namespace App\Http\Controllers\Stores;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Controllers\Controller;
use App\Models\Finance\Item\Category ;
use App\Models\Finance\Item\SubCategory;
use App\Models\Org\UOM;
use App\Models\Org\SupplierTolarance;

use App\Libraries\CapitalizeAllFields;


class SupplierTolaranceController extends Controller{

  public function __construct()
  {
    //add functions names to 'except' paramert to skip authentication
    $this->middleware('jwt.verify', ['except' => ['index']]);
  }

  public function index(Request $request)
  {
    $type = $request->type;

    if($type == 'datatable')   {
      $data = $request->all();
      return response($this->datatable_search($data));
    }
    else if($type == 'auto')    {
      $search = $request->search;
      return response($this->autocomplete_search($search));
    }
    //edited********
    else if($type == 'auto_main_cat')    {
      $search = $request->search;
      return response($this->autocomplete_search_main_cat($search));
    }
    else if($type == 'auto_sub_cat')    {
      $search = $request->search;
      return response($this->autocomplete_search_sub_cat($search));
    }
      else{
      $active = $request->active;
      $fields = $request->fields;
      return response([
        'data' => $this->list($active , $fields)
      ]);
    }
  }


      //create a shipment term
      public function store(Request $request)
      {
          $suplierTolarnce = new SupplierTolarance();
          $suplierTolarnce->fill($request->all());
          $capitalizeAllFields=CapitalizeAllFields::setCapitalAll($suplierTolarnce);
          $suplierTolarnce->status = 1;
          $suplierTolarnce->save();

          return response([ 'data' => [
            'message' => ' Tolarnce saved successfully',
            'message' => ' Tolerance saved successfully',
            'suplierTolarnce' =>$suplierTolarnce
            ]
          ], Response::HTTP_CREATED );
      }

      //get customer size grid
      /*public function show($id)
      {
          $customerSizeGrid = SupplierTolarance::find($id);
          if($customerSizeGrid == null)
            throw new ModelNotFoundException("Requested Customer Size not found", 1);
          else
            return response([ 'data' => $customerSizeGrid ]);
      }*/


   private function autocomplete_search_main_cat($sersch){

     	$category_lists = Category::select('category_id','category_name')
   		->where([['category_name', 'like', '%' .$sersch. '%'],]) ->get();
   		return 	$category_lists ;

   }

   private function autocomplete_search_sub_cat($sersch){

       $sub_category_lists = SubCategory::select('subcategory_id','subcategory_name')
       ->where([['subcategory_name', 'like', '%' .$sersch. '%'],]) ->get();
       return 	$sub_category_lists ;

   }

         //deactivate a ship term
         public function destroy($id)
         {
             $supplierTolarance =SupplierTolarance::where('id', $id)->update(['status' => 0]);
             return response([
               'data' => [
                 'message' => 'Deactivated successfully.',
                 'supplierTolarance' => $supplierTolarance
               ]
             ] , Response::HTTP_NO_CONTENT);
         }

         //validate anything based on requirements
         public function validate_data(Request $request){
           $for = $request->for;

           if($for == 'duplicate')
           {
             //print_r( $request->all());
             return response($this->validate_duplicate_code($request->id , $request->supplier_id,
             $request->category_id,$request->subcategory_id,$request->uom_id,$request->qty,$request->min,$request->max));
           }
         }

         //customer name refeerd as customer id others also like that
         private function validate_duplicate_code($id, $supplier_id, $category_id, $subcategory_id,$uom_id,$qty,$min,$max)
       {
         $supplierTolarance =SupplierTolarance :: where([['supplier_id','=',$supplier_id],['category_id','=',$category_id],['subcategory_id','=',$subcategory_id],['uom_id','=',$uom_id],['qty','=',$qty],['min','=',$min],['max','=',$max]]) -> first();


         if($supplierTolarance == null){
           echo json_encode(array('status' => 'success'));
         }
         else if($supplierTolarance->id == $id){
           echo json_encode(array('status' => 'success'));
         }
         else {
           echo json_encode(array('status' => 'error','message' => 'Record already exists'));
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

     /*$customerSizeGrid_list = CustomerSizeGrid::select('*')
     ->where('customer_id'  , 'like', $search.'%' )
     ->orderBy($order_column, $order_type)
     ->offset($start)->limit($length)->get();

     $customerSizeGrid_count = CustomerSizeGrid::where('customer_id'  , 'like', $search.'%' )
     ->count();

     return [
         "draw" => $draw,
         "recordsTotal" => $customerSizeGrid_count,
         "recordsFiltered" => $customerSizeGrid_count,
         "data" =>   $customerSizeGrid_list
     ];*/

     $supplierTolarance_list= SupplierTolarance::join('org_supplier', 'org_supplier_tolarance.supplier_id', '=', 'org_supplier.supplier_id')
     ->join('item_category', 'org_supplier_tolarance.category_id', '=', 'item_category.category_id')
     ->join('item_subcategory', 'org_supplier_tolarance.subcategory_id', '=', 'item_subcategory.subcategory_id')
     ->join('org_uom', 'org_supplier_tolarance.uom_id', '=', 'org_uom.uom_id')
     ->select('org_supplier_tolarance.*','org_supplier.supplier_name', 'item_category.category_name','item_subcategory.subcategory_name','org_uom.uom_code')
     ->where('supplier_name','like',$search.'%')
     ->orWhere('category_name', 'like', $search.'%')
     ->orWhere('subcategory_name', 'like', $search.'%')
     ->orWhere('uom_code', 'like', $search.'%')
     ->orderBy($order_column, $order_type)
     ->offset($start)->limit($length)->get();

      $supplierTolarance_list_count= SupplierTolarance::join('org_supplier', 'org_supplier_tolarance.supplier_id', '=', 'org_supplier.supplier_id')
     ->join('item_category', 'org_supplier_tolarance.category_id', '=', 'item_category.category_id')
     ->join('item_subcategory', 'org_supplier_tolarance.subcategory_id', '=', 'item_subcategory.subcategory_id')
     ->join('org_uom', 'org_supplier_tolarance.uom_id', '=', 'org_uom.uom_id')
     ->select('org_supplier_tolarance.*','org_supplier.supplier_name', 'item_category.category_name','item_subcategory.subcategory_name','org_uom.uom_code')
     ->where('supplier_name','like',$search.'%')
     ->orWhere('category_name', 'like', $search.'%')
     ->orWhere('subcategory_name', 'like', $search.'%')
     ->orWhere('uom_code', 'like', $search.'%')
     ->count();
     return [
         "draw" => $draw,
         "recordsTotal" => $supplierTolarance_list_count,
         "recordsFiltered" =>$supplierTolarance_list_count,
         "data" =>$supplierTolarance_list
     ];


   }








}
