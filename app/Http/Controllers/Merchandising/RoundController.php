<?php

namespace App\Http\Controllers\Merchandising;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Controllers\Controller;
use App\Models\Merchandising\Round;
use Exception;
use App\Libraries\AppAuthorize;

class RoundController extends Controller
{
    var $authorize = null;

    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => ['index']]);
      $this->authorize = new AppAuthorize();
    }

    //get Round list
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


    //create a Round
    public function store(Request $request)
    {
      if($this->authorize->hasPermission('ROUND_MANAGE'))//check permission
      {
        $round = new Round();
        if($round->validate($request->all()))
        {
          $round->fill($request->all());
          $round->save();

          return response([ 'data' => [
            'message' => 'Round was saved successfully',
            'round' => $round
            ]
          ], Response::HTTP_CREATED );
        }
        else
        {
            $errors = $round->errors();// failure, get errors
            return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }


    //get a Round
    public function show($id)
    {
      if($this->authorize->hasPermission('ROUND_MANAGE'))//check permission
      {
        $round = Round::find($id);
        if($round == null)
          throw new ModelNotFoundException("Requested Round not found", 1);
        else
          return response([ 'data' => $round ]);
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }

    //get filtered fields only
    private function list($active = 0 , $fields = null)
    {
      $query = null;
      if($fields == null || $fields == '') {
        $query = Round::select('*');
      }
      else{
        $fields = explode(',', $fields);
        $query = Round::select($fields);
        if($active != null && $active != ''){
          $query->where([['status', '=', $active]]);
        }
      }
      return $query->get();
    }

    //search Rounds for autocomplete
    private function autocomplete_search($search)
  	{
  		$round_lists = Round::select('round_id')
  		->where([['round_id', 'like', '%' . $search . '%']]) ->get();
  		return $round_lists;
  	}


    //get searched Features for datatable plugin format
    private function datatable_search($data)
    {
      if($this->authorize->hasPermission('ROUND_MANAGE'))//check permission
      {
        $start = $data['start'];
        $length = $data['length'];
        $draw = $data['draw'];
        $search = $data['search']['value'];
        $order = $data['order'][0];
        $order_column = $data['columns'][$order['column']]['data'];
        $order_type = $order['dir'];

        $round_list = Round::select('*')
        ->where('round_id'  , 'like', $search.'%' )
        ->orderBy($order_column, $order_type)
        ->offset($start)->limit($length)->get();

        $round_count = Round::where('round_id'  , 'like', $search.'%' )
        ->count();

        return [
            "draw" => $draw,
            "recordsTotal" => $round_count,
            "recordsFiltered" => $round_count,
            "data" => $round_list
        ];
      }
      else{
        return response($this->authorize->error_response(), 401);
      }      
    }

}
