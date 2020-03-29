<?php

namespace App\Http\Controllers\Merchandising;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Controllers\Controller;
use App\Models\Merchandising\BOMStage;
use App\Models\Merchandising\Costing\Costing;
use App\Models\IE\ComponentSMVHeader;
use Exception;
use App\Libraries\AppAuthorize;

class BOMStageController extends Controller
{
    var $authorize = null;

    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => ['index']]);
      $this->authorize = new AppAuthorize();
    }

    //get Feature list
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
      else {
        $active = $request->active;
        $fields = $request->fields;
        return response([
          'data' => $this->list($active , $fields)
        ]);
      }
    }


    //create a BOMStage
    public function store(Request $request)
    {
      if($this->authorize->hasPermission('BOM_STAGE_MANAGE'))//check permission
      {
        $bomstage = new BOMStage();
        if($bomstage->validate($request->all()))
        {
          $bomstage->fill($request->all());
          $bomstage->status = 1;
          $bomstage->bom_stage_description=strtoupper($bomstage->bom_stage_description);
          $bomstage->save();

          return response([ 'data' => [
            'message' => 'BOM Stage saved successfully',
            'bomstage' => $bomstage,
            'status'=>'1'
            ]
          ], Response::HTTP_CREATED );
        }
        else
        {
          $errors = $bomstage->errors();// failure, get errors
          $errors_str = $bomstage->errors_tostring();
          return response(['errors' => ['validationErrors' => $errors, 'validationErrorsText' => $errors_str]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }


    //get a Feature
    public function show($id)
    {
      if($this->authorize->hasPermission('BOM_STAGE_MANAGE'))//check permission
      {
        $bomstage = BOMStage::find($id);
        if($bomstage == null)
          throw new ModelNotFoundException("Requested BOM Stage not found", 1);
        else
          return response([ 'data' => $bomstage ]);
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }


    //update a Feature
    public function update(Request $request, $id)
    {
      if($this->authorize->hasPermission('BOM_STAGE_MANAGE'))//check permission
      {
        $bulkCostingFeatureDetails = Costing::where([['bom_stage_id','=',$id]])->first();
        $ComponentSmv=ComponentSMVHeader::where([['bom_stage_id','=',$id]])->first();
        if($bulkCostingFeatureDetails!=null||$ComponentSmv!=null){
          return response([ 'data' => [
            'status' => '0',
                ]]);
        }
        else if($bulkCostingFeatureDetails==null&&$ComponentSmv==null){
        $bomstage = BOMStage::find($id);
        if($bomstage->validate($request->all()))
        {
          $bomstage->fill($request->all());
          $bomstage->bom_stage_description=strtoupper($bomstage->bom_stage_description);
          $bomstage->save();

          return response([ 'data' => [
            'message' => 'BOM Stage updated successfully',
            'bomstage' => $bomstage,
            'status'=>'1'
          ]]);
        }
      }
        else
        {
          $errors = $bomstage->errors();// failure, get errors
          return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }


    //deactivate a Feature
    public function destroy($id)
    {
      if($this->authorize->hasPermission('BOM_STAGE_DELETE'))//check permission
      {
        $bulkCostingFeatureDetails=Costing::where([['bom_stage_id','=',$id]])->first();
        if($bulkCostingFeatureDetails!=null){
          return response([
            'data'=>[
              'status'=>'0',
            ]
          ]);
        }
        $bomstage = BOMStage::where('bom_stage_id', $id)->update(['status' => 0]);
        return response([
          'data' => [
            'message' => 'BOM Stage was deactivated successfully.',
            'bomstage' => $bomstage
          ]
        ]);
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
        return response($this->validate_duplicate_code($request->bom_stage_id , $request->bom_stage_description));
      }
    }


    //check Feature code already exists
    private function validate_duplicate_code($id , $code)
    {
      $bomstage = BOMStage::where('bom_stage_description','=',$code)->first();
      if($bomstage == null){
        return ['status' => 'success'];
      }
      else if($bomstage->bom_stage_id == $id){
        return ['status' => 'success'];
      }
      else {
        return ['status' => 'error','message' => 'BOM Stage already exists'];
      }
    }


    //get filtered fields only
    private function list($active = 0 , $fields = null)
    {
      $query = null;
      if($fields == null || $fields == '') {
        $query = BOMStage::select('*');
      }
      else{
        $fields = explode(',', $fields);
        $query = BOMStage::select($fields);
        if($active != null && $active != ''){
          $query->where([['status', '=', $active]]);
        }
      }
      return $query->get();
    }

    //search Size for autocomplete
    private function autocomplete_search($search)
  	{
  		$bomstage_lists = BOMStage::select('bom_stage_id','bom_stage_description')
  		->where([['bom_stage_description', 'like', '%' . $search . '%']])
      ->where('status','=',1) ->get();
  		return $bomstage_lists;
  	}


    //get searched Features for datatable plugin format
    private function datatable_search($data)
    {
      if($this->authorize->hasPermission('BOM_STAGE_MANAGE'))//check permission
      {
        $start = $data['start'];
        $length = $data['length'];
        $draw = $data['draw'];
        $search = $data['search']['value'];
        $order = $data['order'][0];
        $order_column = $data['columns'][$order['column']]['data'];
        $order_type = $order['dir'];

        $bomstage_list = BOMStage::select('*')
        ->where('bom_stage_description'  , 'like', $search.'%' )
        ->orderBy($order_column, $order_type)
        ->offset($start)->limit($length)->get();

        $bomstage_count = BOMStage::where('bom_stage_description'  , 'like', $search.'%' )
        ->count();

        return [
            "draw" => $draw,
            "recordsTotal" => $bomstage_count,
            "recordsFiltered" => $bomstage_count,
            "data" => $bomstage_list
        ];
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }

}
