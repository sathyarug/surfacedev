<?php

namespace App\Http\Controllers\Merchandising;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Controllers\Controller;
use App\Models\Merchandising\MasAction;

class TnaMasterController extends Controller
{
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

    //update a action
    public function update(Request $request, $id)
    {
//        print_r($request->all());exit;
        $action = MasAction::find($id);
        if($action->validate($request->all()))
        {
            $action->fill($request->except('action_name'));
            $action->save();

            return response([ 'data' => [
                'message' => 'Season was updated successfully',
                'season' => $action
            ]]);
        }
        else
        {
            $errors = $action->errors();// failure, get errors
            return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }


    //deactivate a action
    public function destroy($id)
    {
        $season = MasAction::where('action_id', $id)->update(['status' => 0]);
        return response([
            'data' => [
                'message' => 'Season was deactivated successfully.',
                'Season' => $season
            ]
        ] , Response::HTTP_NO_CONTENT);
    }

    //get searched Sections for datatable plugin format
    private function datatable_search($data)
    {
        $start = $data['start'];
        $length = $data['length'];
        $draw = $data['draw'];
        $search = $data['search']['value'];
        $order = $data['order'][0];
        $order_column = $data['columns'][$order['column']]['data'];
        $order_type = $order['dir'];

        $section_list = MasAction::select('*')
            ->where('action_name'  , 'like', $search.'%' )
            ->orWhere('action_description'  , 'like', $search.'%' )
            ->orderBy('status',$order_column, $order_type)
            ->offset($start)->limit($length)->get();

        $section_count = MasAction::where('action_name'  , 'like', $search.'%' )
            ->orWhere('action_description'  , 'like', $search.'%' )
            ->count();

        return [
            "draw" => $draw,
            "recordsTotal" => $section_count,
            "recordsFiltered" => $section_count,
            "data" => $section_list
        ];
    }

    //search Section for autocomplete
    private function autocomplete_search($search)
    {
        $section_lists = MasAction::select('section_id','section_name')
            ->where([['section_name', 'like', '%' . $search . '%'],]) ->get();
        return $section_lists;
    }

    //get filtered fields only
    private function list($active = 0 , $fields = null)
    {
        $query = null;
        if($fields == null || $fields == '') {
            $query = MasAction::select('action_id','status','action_name','action_description','offset');
        }
        else{
            $fields = explode(',', $fields);
            $query = Section::select($fields);
            if($active != null && $active != ''){
                $query->where([['status', '=', $active]]);
            }
        }
        return $query->get();
    }
    //get a Section
    public function show($id)
    {
        $section = MasAction::find($id);
        if($section == null)
            throw new ModelNotFoundException("Requested section not found", 1);
        else
            return response([ 'data' => $section ]);
    }

    //create a Action
    public function store(Request $request)
    {
        $season = new MasAction();
        if($season->validate($request->all()))
        {
            $season->fill($request->all());
            $season->status = 1;
            $season->save();

            return response([ 'data' => [
                'message' => 'Action was saved successfully',
                'season' => $season
            ]
            ], Response::HTTP_CREATED );
        }
        else
        {
            $errors = $season->errors();// failure, get errors
            return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
