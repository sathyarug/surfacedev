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
use App\Models\IE\SMVUpdateHistory;

class SMVUpdateHistoryController extends Controller
{
    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => ['index']]);
    }

    //get SMVUpdate History list
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
      else{
        return response([]);
      }
    }


    //create SMVUpdate History
    public function store(Request $request)
    {
      $smvupdatehis = new SMVUpdateHistory();
      if($smvupdatehis->validate($request->all()))
      {
        $smvupdatehis->fill($request->all());
        $smvupdatehis->version = 1;
        $smvupdatehis->save();


        return response([ 'data' => [

          'smvupdatehis' => $smvupdatehis
          ]
        ], Response::HTTP_CREATED );
      }
      else
      {
          $errors = $smvupdatehis->errors();// failure, get errors
          return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
      }
    }


    //get a SMVUpdate History
    public function show($id)
    {

      $smvupdatehis = SMVUpdateHistory::with(['silhouette'])->find($id);

      if($smvupdatehis == null)
        throw new ModelNotFoundException("Requested SMV not found", 1);
      else
        return response([ 'data' => $smvupdatehis ]);
    }

    //create SMVUpdate History
    public function update(Request $request)
    {
      $smvupdatehis = new SMVUpdateHistory();
      if($smvupdatehis->validate($request->all()))
      {
        $smvupdatehis->fill($request->all());
        $smvupdatehis->version = ($request->version)+1;
        $smvupdatehis->save();

        return response([ 'data' => [

          'smvupdatehis' => $smvupdatehis
          ]
        ], Response::HTTP_CREATED );
      }
      else
      {
          $errors = $smvupdatehis->errors();// failure, get errors
          return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
      }
    }

    //deactivate a SMVUpdate History
    public function destroy($id)
    {
      $smvupdatehis = SMVUpdateHistory::where('smv_his_id', $id)->update(['status' => 0]);
      return response([
        'data' => [
          'smvupdatehis' => $smvupdatehis
        ]
      ] , Response::HTTP_NO_CONTENT);
    }



    //search SMVUpdate History for autocomplete
    private function autocomplete_search($search)
    {
      $smvupdate_history_lists = SMVUpdateHistory::join('cust_customer', 'ie_smv_his.customer_id', '=' , 'cust_customer.customer_id')
      ->join('product_silhouette', 'ie_smv_his.product_silhouette_id', '=' , 'product_silhouette.product_silhouette_id')
      ->join('cust_division', 'ie_smv_his.division_id', '=' , 'cust_division.division_id')
      ->select('smv_his_id', 'cust_customer.customer_name')
      ->where([['cust_customer.customer_name', 'like', '%' . $search . '%'],]) ->get();
      return $smvupdate_history_lists;
    }


    //get searched SMVUpdate History for datatable plugin format
    private function datatable_search($data)
    {
      $start = $data['start'];
      $length = $data['length'];
      $draw = $data['draw'];
      $search = $data['search']['value'];
      $order = $data['order'][0];
      $order_column = $data['columns'][$order['column']]['data'];
      $order_type = $order['dir'];

      $smvupdate_history_list = SMVUpdateHistory::join('product_silhouette', 'ie_smv_his.product_silhouette_id', '=' , 'product_silhouette.product_silhouette_id')
      ->select('ie_smv_his.*', 'product_silhouette.product_silhouette_description')
      ->where('product_silhouette.product_silhouette_description'  , 'like', $search.'%' )
      ->orwhere('ie_smv_his.version'  , 'like', $search.'%' )
      ->orderBy($order_column, $order_type)
      ->offset($start)->limit($length)->get();

      $smvupdate_history_count = SMVUpdateHistory::join('product_silhouette', 'ie_smv_his.product_silhouette_id', '=' , 'product_silhouette.product_silhouette_id')
      ->select('ie_smv_his.*', 'product_silhouette.product_silhouette_description')
      ->where('product_silhouette.product_silhouette_description'  , 'like', $search.'%' )
      ->orwhere('ie_smv_his.version'  , 'like', $search.'%' )
      ->count();

      return [
          "draw" => $draw,
          "recordsTotal" => $smvupdate_history_count,
          "recordsFiltered" => $smvupdate_history_count,
          "data" => $smvupdate_history_list
      ];
    }

    public function loadSmv(Request $request) {
//        print_r(Customer::where('customer_name', 'LIKE', '%'.$request->search.'%')->get());exit;
        try{
            echo json_encode(SMVUpdateHistory::join('cust_customer', 'ie_smv_his.customer_id', '=' , 'cust_customer.customer_id')
            ->join('product_silhouette', 'ie_smv_his.product_silhouette_id', '=' , 'product_silhouette.product_silhouette_id')
            ->where('cust_customer.customer_name'  , 'LIKE', '%'.$request->search.'%')->get());
              // Customer::where('customer_name', 'LIKE', '%'.$request->search.'%')->get());
//            return CustomerResource::collection(Customer::where('customer_name', 'LIKE', '%'.$request->search.'%')->get() );
        }
        catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['error' => 'could_not_create_token'], 500);
        }
//        $customer_list = Customer::all();
//        echo json_encode($customer_list);
    }





}
