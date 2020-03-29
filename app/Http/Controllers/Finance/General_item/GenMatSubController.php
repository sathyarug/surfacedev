<?php

namespace App\Http\Controllers\Finance\General_item;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Models\Finance\General_item\gen_mat_sub_category;
use Exception;

class GenMatSubController extends Controller
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
        //
        //dd($request->type);
        $type = $request->type;
        //$data = $request->all();
        //dd($data['id']);
    
        if($type == 'datatable')   {
          $data = $request->all();
          return response($this->datatable_search($data));
        }
        else if($type == 'auto')    {
          $search = $request->search;
          return response($this->autocomplete_search($search));
        }
        else if($type == 'load_sub_category')    {
          $data = $request->all();
          return response($this->load_sub_cat($data));
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
      dd("AA");
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      dd("AA2");
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
      //dd("AA3");
      $uom = gen_mat_sub_category::select('uom')
      ->where([['category_id', '=', $id],]) ->get();
      return $uom ;
      
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
      dd("AA4");
        //
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
      dd("AA5");
        //
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
        $query = gen_mat_sub_category::select('*');
      }
      else{
        $fields = explode(',', $fields);
        $query = gen_mat_sub_category::select($fields);
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

      private function load_sub_cat($data)
      {
          $gen_mat_sub_category_lists = gen_mat_sub_category::select('category_id','category_name','category_code')
          ->where([['mat_main_cat_id', '=', $data['id']],]) ->get();
          return $gen_mat_sub_category_lists;
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

      $dep_list = gen_mat_sub_category::select('*')
      ->where('category_code'  , 'like', $search.'%' )
      ->orWhere('category_name','like',$search.'%')
      ->orderBy($order_column, $order_type)
      ->offset($start)->limit($length)->get();

      $dep_count = gen_mat_sub_category::where('category_code'  , 'like', $search.'%' )
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
