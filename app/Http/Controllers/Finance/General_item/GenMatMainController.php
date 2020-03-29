<?php

namespace App\Http\Controllers\Finance\General_item;


use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Models\Finance\General_item\gen_mat_main_category;
use Exception;

class GenMatMainController extends Controller
{
    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => ['index']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //dd("xsdd");
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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        dd('sdsd5');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        dd('sdsd4');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        dd('sdsd');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        dd('sdsd2');
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
        //
        dd('sdsd23');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        dd('sdsd4');
    }

    public function validate_data(Request $request){
        $for = $request->for;
        if($for == 'duplicate')
        {
          return response($this->validate_duplicate_code($request->category_id , $request->category_code));
        }
      }
  
  
      //check Department code already exists
      private function validate_duplicate_code($id , $code)
      {
        $department = Department::where('category_code','=',$code)->first();
        if($department == null){
          return ['status' => 'success'];
        }
        else if($department->Department_id == $id){
          return ['status' => 'success'];
        }
        else {
          return ['status' => 'error','message' => 'Department code already exists'];
        }
      }
  
  
      //get filtered fields only
      private function list($active = 0 , $fields = null)
      {
        $query = null;
        if($fields == null || $fields == '') {
          $query = gen_mat_main_category::select('*');
        }
        else{
          $fields = explode(',', $fields);
          $query = gen_mat_main_category::select($fields);
          if($active != null && $active != ''){
            $query->where([['status', '=', $active]]);
          }
        }
        return $query->get();
      }
  
      //search Department for autocomplete
      private function autocomplete_search($search)
        {
            $department_lists = Department::select('category_id','category_name')
            ->where([['category_name', 'like', '%' . $search . '%'],]) ->get();
            return $department_lists;
        }
  
  
      //get searched Departments for datatable plugin format
      private function datatable_search($data)
      {
        $start = $data['start'];
        $length = $data['length'];
        $draw = $data['draw'];
        $search = $data['search']['value'];
        $order = $data['order'][0];
        $order_column = $data['columns'][$order['column']]['data'];
        $order_type = $order['dir'];
  
        $dep_list = gen_mat_main_category::select('*')
        ->where('category_code'  , 'like', $search.'%' )
        ->orWhere('category_name','like',$search.'%')
        ->orderBy($order_column, $order_type)
        ->offset($start)->limit($length)->get();
  
        $dep_count = gen_mat_main_category::where('category_code'  , 'like', $search.'%' )
        ->orWhere('category_name','like',$search.'%')
        ->count();
  
        return [
            "draw" => $draw,
            "recordsTotal" => $dep_count,
            "recordsFiltered" => $dep_count,
            "data" => $dep_list
        ];
      }
}
