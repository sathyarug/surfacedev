<?php

namespace App\Http\Controllers\IE;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use App\Models\Org\Customer;
use App\Models\Org\Division;
use App\Models\Org\Silhouette;
use App\Models\IE\SMVUpdate;


class SMVUpdateController extends Controller
{
  public function __construct()
  {
    //add functions names to 'except' paramert to skip authentication
    $this->middleware('jwt.verify', ['except' => ['index']]);
  }

  //get SMVUpdate list
  public function index(Request $request)
  {
    $type = $request->type;
    if ($type == 'datatable') {
      $data = $request->all();
      return response($this->datatable_search($data));
    } else if ($type == 'auto') {
      $search = $request->search;
      return response($this->autocomplete_search($search));
    } else {
      return response([]);
    }
  }

  //create a SMVUpdate
  public function store(Request $request)
  {
    $smvupdate = new SMVUpdate();
    if ($smvupdate->validate($request->all())) {
      $smvupdate->fill($request->all());
      $smvupdate->status = 1;
      $smvupdate->version = 1;
      $smvupdate->save();

      return response([
        'data' => [
          'message' => 'SMV saved successfully',
          'smvupdate' => $smvupdate
        ]
      ], Response::HTTP_CREATED);
    } else {
      $errors = $smvupdate->errors(); // failure, get errors
      return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

  }


  //get a SMVUpdate
  public function show($id)
  {

    //$smvupdate = SMVUpdate::with(['customer', 'silhouette', 'division'])->find($id);
    $smvupdate = SMVUpdate::with(['silhouette'])->find($id);

    if ($smvupdate == null)
    throw new ModelNotFoundException("Requested SMV not found", 1);
    else
    return response(['data' => $smvupdate]);
  }


  //update a SMVUpdate
  public function update(Request $request, $id)
  {
    $smvupdate = SMVUpdate::find($id);

    // $is_exists_in_com_smv = DB::table('ie_component_smv_header')
    // ->join('ie_component_smv_details', 'ie_component_smv_details.smv_component_header_id', '=', 'ie_component_smv_header.smv_component_header_id')
    // ->join('style_creation', 'style_creation.style_id', '=', 'ie_component_smv_header.style_id')
    // ->join('smv_update', 'smv_update.product_silhouette_id', '=', 'ie_component_smv_details.product_silhouette_id')
    // //->where('style_creation.customer_id', '=', $smvupdate->customer_id)
    // //->where('style_creation.division_id', '=', $smvupdate->division_id)
    // ->where('ie_component_smv_details.product_silhouette_id', '=', $smvupdate->product_silhouette_id)
    // ->where('ie_component_smv_header.status', '=', 1)
    // ->exists();
    //
    // if ($is_exists_in_com_smv == false) {

      if ($smvupdate->validate($request->all())) {
        $smvupdate->fill($request->except('smv_id', 'customer_id', 'division_id', 'product_silhouette_id'));
        $smvupdate->where('smv_id', $id)->update(['version' => $request->version + 1]);
        $smvupdate->save();

        return response(['data' => [
          'message' => 'SMV updated successfully',
          'smvupdate' => $smvupdate,
          'status' => '1'
          ]]);
        } else {
          $errors = $smvupdate->errors(); // failure, get errors
          return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
      // } else {
      //   return response(['data' => [
      //     'message' => 'Min and Max SMV Already Used in Component SMV',
      //     'smvupdate' => $smvupdate,
      //     'status' => '0'
      //     ]]);
      //   }
      }


      //deactivate a SMVUpdate
      public function destroy($id)
      {
        $smvupdate = SMVUpdate::where('smv_id', $id)->update(['status' => 0]);
        return response([
          'data' => [
            'message' => 'SMV deactivated successfully.',
            'smvupdate' => $smvupdate
          ]
        ], Response::HTTP_NO_CONTENT);
      }


      //validate anything based on requirements
      public function validate_data(Request $request)
      {

        $for = $request->for;
        if ($for == 'duplicate') {
          return response($this->validate_duplicate_code(
            $request->smv_id,
            $request->product_silhouette_description));
        }
      }


      // public function customer_divisions(Request $request) {
      //
      //     $customer_id = $request->customer_id;
      //
      //
      //       $selected = Division::select('division_id','division_description')
      //       ->whereIn('division_id' , function($selected) use ($customer_id){
      //           $selected->select('division_id')
      //           ->from('org_customer_divisions')
      //           ->where('customer_id', $customer_id);
      //       })->get();
      //       return response([ 'data' => $selected]);
      //
      //
      // }




      //check SMVUpdate already exists
      private function validate_duplicate_code($id, $silDes)
      {

        $smvupdate = SMVUpdate::where('product_silhouette_id', '=', $silDes)->first();
          //dd($smvupdate);
        if ($smvupdate == null) {
          echo json_encode(array('status' => 'success'));
        }
        else if($smvupdate->smv_id == $id){
            return ['status' => 'success'];
        }
        else {
          echo json_encode(array('status' => 'error', 'message' => 'SMV record already exists'));
        }
      }


      //search customer for autocomplete
      private function autocomplete_search($search)
      {
        $smvupdate_lists = SMVUpdate::join('product_silhouette', 'smv_update.product_silhouette_id', '=', 'product_silhouette.product_silhouette_id')
        ->select('smv_id', 'product_silhouette.product_silhouette_description')
        ->where([['product_silhouette.product_silhouette_description', 'like', '%' . $search . '%'],])->get();
        return $smvupdate_lists;
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

        $smvupdate_list = SMVUpdate::join('product_silhouette', 'smv_update.product_silhouette_id', '=', 'product_silhouette.product_silhouette_id')
        ->select('smv_update.*', 'product_silhouette.product_silhouette_description')
        ->where('product_silhouette.product_silhouette_description', 'like', $search . '%')
        ->orwhere('smv_update.version', 'like', $search . '%')
        ->orderBy($order_column, $order_type)
        ->offset($start)->limit($length)->get();

        $smvupdate_count = SMVUpdate::join('product_silhouette', 'smv_update.product_silhouette_id', '=', 'product_silhouette.product_silhouette_id')
        ->select('smv_update.*', 'product_silhouette.product_silhouette_description')
        ->where('product_silhouette.product_silhouette_description', 'like', $search . '%')
        ->orwhere('smv_update.version', 'like', $search . '%')
        ->count();

        return [
          "draw" => $draw,
          "recordsTotal" => $smvupdate_count,
          "recordsFiltered" => $smvupdate_count,
          "data" => $smvupdate_list
        ];
      }

      public function loadSmv(Request $request)
      {
        //        print_r(Customer::where('customer_name', 'LIKE', '%'.$request->search.'%')->get());exit;
        try {
          echo json_encode(SMVUpdate::join('cust_customer', 'smv_update.customer_id', '=', 'cust_customer.customer_id')
          ->join('product_silhouette', 'smv_update.product_silhouette_id', '=', 'product_silhouette.product_silhouette_id')
          ->where('cust_customer.customer_name', 'LIKE', '%' . $request->search . '%')->get());
          // Customer::where('customer_name', 'LIKE', '%'.$request->search.'%')->get());
          //            return CustomerResource::collection(Customer::where('customer_name', 'LIKE', '%'.$request->search.'%')->get() );
        } catch (JWTException $e) {
          // something went wrong whilst attempting to encode the token
          return response()->json(['error' => 'could_not_create_token'], 500);
        }
        //        $customer_list = Customer::all();
        //        echo json_encode($customer_list);
      }
    }
