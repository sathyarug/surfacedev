<?php

namespace App\Http\Controllers\Org\Location;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Controllers\Controller;
use App\Models\Org\Location\Source;
use App\Models\Org\Location\Cluster;
use App\Libraries\AppAuthorize;
use App\Libraries\CapitalizeAllFields;
class SourceController extends Controller
{
    var $authorize = null;

    public function __construct(Request $request)
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => ['index']]);
      $this->authorize = new AppAuthorize();
    }

    //get Source list
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


    //create a Source
    public function store(Request $request)
    {
      if($this->authorize->hasPermission('SOURCE_MANAGE'))//check permission
      {
        $source = new Source();
        if($source->validate($request->all()))
        {
          $source->fill($request->all());
          //$capitalizeAllFields=CapitalizeAllFields::setCapitalAll($source);
          $source->status = 1;
          $source->save();

          return response([ 'data' => [
            'message' => 'Parent Company saved successfully',
            'source' => $source
            ]
          ], Response::HTTP_CREATED );
        }
        else
        {
          $errors = $source->errors();// failure, get errors
          $errors_str = $source->errors_tostring();
          return response(['errors' => ['validationErrors' => $errors, 'validationErrorsText' => $errors_str]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }


    //get a Source
    public function show($id)
    {
      if($this->authorize->hasPermission('SOURCE_MANAGE'))//check permission
      {
        $source = Source::find($id);
        if($source == null)
          throw new ModelNotFoundException("Requested source not found", 1);
        else
          return response([ 'data' => $source ]);
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }


    //update a Source
    public function update(Request $request, $id)
    {
      if($this->authorize->hasPermission('SOURCE_MANAGE'))//check permission
      {
        $source = Source::find($id);
        if($source->validate($request->all()))
        {
          $check_cluster = Cluster::where([['status', '=', '1'],['source_id','=',$id]])->first();
          if($check_cluster != null)
          {
            return response([
              'data'=>[
                'status'=>'0',
              ]
            ]);
          }else{
          $source->fill($request->except('source_code'));
          //$capitalizeAllFields=CapitalizeAllFields::setCapitalAll($source);
          $source->save();

          return response([ 'data' => [
            'message' => 'Parent Company updated successfully',
            'source' => $source
          ]]);
        }
        }
        else
        {
          $errors = $source->errors();// failure, get errors
          $errors_str = $source->errors_tostring();
          return response(['errors' => ['validationErrors' => $errors, 'validationErrorsText' => $errors_str]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }


    //deactivate a Source
    public function destroy($id)
    {
      if($this->authorize->hasPermission('SOURCE_DELETE'))//check permission
      {

        $check_cluster = Cluster::where([['status', '=', '1'],['source_id','=',$id]])->first();
        if($check_cluster != null)
        {
          return response([
            'data'=>[
              'status'=>'0',
            ]
          ]);
        }else{

        $source = Source::where('source_id', $id)->update(['status' => 0]);
        return response([
          'data' => [
            'message' => 'Source was deactivated successfully.',
            'source' => $source
          ]
        ]);

      }

      }
      else {
        return response($this->authorize->error_response(), 401);
      }
    }


    //validate anything based on requirements
    public function validate_data(Request $request){
      $for = $request->for;
      if($for == 'duplicate')
      {
        return response($this->validate_duplicate_code($request->source_id , $request->source_code));
      }
    }


    //check Source code already exists
    private function validate_duplicate_code($id , $code)
    {
      $source = Source::where('source_code','=',$code)->first();
      if($source == null){
        return ['status' => 'success'];
      }
      else if($source->source_id == $id){
        return ['status' => 'success'];
      }
      else {
        return ['status' => 'error','message' => 'Source code already exists'];
      }
    }


    //get filtered fields only
    private function list($active = 0 , $fields = null)
    {
        $query = null;
        if($fields == null || $fields == '') {
          $query = Source::select('*');
        }
        else{
          $fields = explode(',', $fields);
          $query = Source::select($fields);
          if($active != null && $active != ''){
            $query->where([['status', '=', $active]]);
          }
        }
        return $query->get();
    }


    //search Source for autocomplete
    private function autocomplete_search($search)
  	{
  		$source_lists = Source::select('source_id','source_name')
  		->where([['source_name', 'like', '%' . $search . '%'],]) ->get();
  		return $source_lists;
  	}


    //get searched Sources for datatable plugin format
    private function datatable_search($data)
    {
      if($this->authorize->hasPermission('SOURCE_MANAGE'))//check permission
      {
        $start = $data['start'];
        $length = $data['length'];
        $draw = $data['draw'];
        $search = $data['search']['value'];
        $order = $data['order'][0];
        $order_column = $data['columns'][$order['column']]['data'];
        $order_type = $order['dir'];

        $source_list = Source::select('*')
        ->where('source_code'  , 'like', $search.'%' )
        ->orWhere('source_name'  , 'like', $search.'%' )
        ->orWhere('source_name'  , 'like', $search.'%' )
        ->orderBy($order_column, $order_type)
        ->offset($start)->limit($length)->get();

        $source_count = Source::where('source_code'  , 'like', $search.'%' )
        ->orWhere('source_name'  , 'like', $search.'%' )
        ->orWhere('source_name'  , 'like', $search.'%' )
        ->count();

        return [
            "draw" => $draw,
            "recordsTotal" => $source_count,
            "recordsFiltered" => $source_count,
            "data" => $source_list
        ];
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }

}
