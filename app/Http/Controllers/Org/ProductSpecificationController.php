<?php

namespace App\Http\Controllers\Org;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Controllers\Controller;
use App\Models\Org\ProductSpecification;
use App\Libraries\AppAuthorize;
use App\Libraries\CapitalizeAllFields;
use Illuminate\Support\Facades\DB;

class  ProductSpecificationController extends Controller
{
    var $authorize = null;

    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => ['index']]);
      $this->authorize = new AppAuthorize();
    }

    //get product specification listerm list
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
      if($this->authorize->hasPermission('PROD_SPEC_MANAGE'))//check permission
      {
        
        $productSpecification = new  ProductSpecification ();
        if($productSpecification->validate($request->all()))
        {
          $productSpecification->fill($request->all());
          $capitalizeAllFields=CapitalizeAllFields::setCapitalAll($productSpecification);
          $productSpecification->status = 1;
          $productSpecification->save();

          return response([ 'data' => [
            'message' => 'Product Type saved successfully',
            'productSpecification' => $productSpecification,
            'status'=>1
            ]
          ], Response::HTTP_CREATED );
        }else{
          $errors = $productSpecification->errors();// failure, get errors
          $errors_str = $productSpecification->errors_tostring();
          return response(['errors' => ['validationErrors' => $errors, 'validationErrorsText' => $errors_str]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }

    //get shipment term
    public function show($id)
    {
      if($this->authorize->hasPermission('PROD_SPEC_MANAGE'))//check permission
      {
        $productSpecification = ProductSpecification::find($id);
        if($productSpecification == null)
          throw new ModelNotFoundException("Requested shipment term not found", 1);
        else
          return response([ 'data' => $productSpecification]);
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }


    //update a shipment term
    public function update(Request $request, $id)
    {
      if($this->authorize->hasPermission('PROD_SPEC_MANAGE'))//check permission
      {
        $is_exists=DB::table('style_creation')->where('product_category_id',$id)->exists();
        if($is_exists==true){

          return response([ 'data' => [
            'message' => 'Product Type Already in Use',
            'status' => '0'
          ]]);
        }
        else {
        $productSpecification =  ProductSpecification::find($id);
        $productSpecification->fill($request->all());
        $capitalizeAllFields=CapitalizeAllFields::setCapitalAll($productSpecification);
        $productSpecification->save();

        return response([ 'data' => [
          'message' => 'Product Type updated successfully',
          'transaction' => $productSpecification,
          'status'=>'1'
        ]]);
      }
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }



    //deactivate a ship term
    public function destroy($id)
    {
      if($this->authorize->hasPermission('PROD_SPEC_DELETE'))//check permission
      {
          $is_exists=DB::table('style_creation')->where('product_category_id',$id)->exists();
        if($is_exists==true){
          return response([ 'data' => [
            'message' => 'Product Type Already in Use',
            'status' => '0'
          ]]);
        }

        else {
        $productSpecification =ProductSpecification::where('prod_cat_id', $id)->update(['status' => 0]);
        return response([ 'data' => [
          'message' => 'Product Type Deactived sucessfully',
          'status' => '1',
          'productSpesication'=>$productSpecification
        ]]);
      }
    }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }
    
    //validate anything based on requirements
    public function validate_data(Request $request){
      $for = $request->for;
      if($for == 'duplicate')
      {
        return response($this->validate_duplicate_name($request->prod_cat_id , $request->prod_cat_description));
      }
      else if($for == 'duplicate-code')
      {
        return response($this->validate_duplicate_code($request->prod_cat_id , $request->category_code));
      }
    }

    //check shipment cterm code code already exists
    private function validate_duplicate_code($id , $code)
    {
       $productSpecification = ProductSpecification::where([['category_code','=',$code]])->first();

      if( $productSpecification  == null){
         echo json_encode(array('status' => 'success'));
      }
      else if( $productSpecification ->prod_cat_id == $id){
         echo json_encode(array('status' => 'success'));
      }
      else {
       echo json_encode(array('status' => 'error','message' => 'Product type code already exists'));
      }
    }


    //check shipment cterm code code already exists
    private function validate_duplicate_name($id , $code)
    {
       $productSpecification = ProductSpecification::where([['prod_cat_description','=',$code]])->first();

      if( $productSpecification  == null){
         echo json_encode(array('status' => 'success'));
      }
      else if( $productSpecification ->prod_cat_id == $id){
         echo json_encode(array('status' => 'success'));
      }
      else {
       echo json_encode(array('status' => 'error','message' => 'Product type already exists'));
      }
    }

    //get filtered fields only
    private function list($active = 0 , $fields = null)
    {
      $query = null;
      if($fields == null || $fields == '') {
        $query = ProductSpecification::select('*');
      }
      else{
        $fields = explode(',', $fields);
        $query = ProductSpecification::select($fields);
        if($active != null && $active != ''){
          $query->where([['status', '=', $active]]);
        }
      }
      return $query->get();
    }


    //search shipment terms for autocomplete
    private function autocomplete_search($search)
  	{
  		$transaction_lists = ProductSpecification::select('prod_cat_description')
  		->where([['prod_cat_description', 'like', '%' . $search . '%'],]) ->get();
  		return $transaction_lists;
  	}


    //get searched ship terms for datatable plugin format
    private function datatable_search($data)
    {
      if($this->authorize->hasPermission('PROD_SPEC_MANAGE'))//check permission
      {
        $start = $data['start'];
        $length = $data['length'];
        $draw = $data['draw'];
        $search = $data['search']['value'];
        $order = $data['order'][0];
        $order_column = $data['columns'][$order['column']]['data'];
        $order_type = $order['dir'];

        $transaction_list = ProductSpecification::select('*')
        ->where('prod_cat_description'  , 'like', $search.'%' )
        ->orderBy($order_column, $order_type)
        ->offset($start)->limit($length)->get();

        $transaction_count = ProductSpecification::where('prod_cat_description'  , 'like', $search.'%' )
        ->count();

        return [
            "draw" => $draw,
            "recordsTotal" => $transaction_count,
            "recordsFiltered" => $transaction_count,
            "data" => $transaction_list
        ];
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }

}
