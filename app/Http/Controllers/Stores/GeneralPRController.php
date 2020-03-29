<?php

namespace App\Http\Controllers\Stores;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\stores\GeneralPR;
use Illuminate\Support\Facades\DB;

class GeneralPRController extends Controller
{
    /**
     * Display a listing of the reGeneralPR.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $type = $request->type;
     
        if($type == 'datatable')   {
        $data = $request->all();
        //dd($data['id']);
            if($data['id']!=0){
                return response($this->datatable_search($data));
            }else{
                return response($this->datatable_search_list($data));
            }
        
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

    /**
     * Show the form for creating a new reGeneralPR.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        dd('CC');
    }

    /**
     * Store a newly created reGeneralPR in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      //dd($request);
        $generalPR = new GeneralPR();
        
      if($generalPR->validate($request->all()))
      {
      
        $generalPR->fill($request->all());
        $generalPR->status = 1;
        //$generalPR->Item_wanted_date = '2018-10-03 21:21:02';
        $generalPR->user_id = 9;
        // dd ($generalPR->Item_wanted_date);
        $generalPR->save();
       
        return response([ 'data' => [
          'message' => 'GeneralPR was saved successfully',
          'generalPR' => $generalPR
          ]
        ], Response::HTTP_CREATED );
      }
      else
      {
          $errors = $generalPR->errors();// failure, get errors
          return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
      }
    }

    /**
     * Display the specified reGeneralPR.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        dd('GG');
    }

    /**
     * Show the form for editing the specified reGeneralPR.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        dd('XX');
    }

    /**
     * Update the specified reGeneralPR in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        dd('ADd');
    }

    /**
     * Remove the specified reGeneralPR from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    private function autocomplete_search($search)
    {
        $GeneralPR_lists = GeneralPR::select('id','department')
        ->where([['department', 'like', '%' . $search . '%'],]) ->get();
        return $GeneralPR_lists;
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

      $GeneralPR_list = GeneralPR::join('stores_gen_pr_detail', 'stores_gen_pr_header.request_no', '=', 'stores_gen_pr_detail.request_id')
      ->join('gen_mat_main_category', 'stores_gen_pr_detail.main_category', '=', 'gen_mat_main_category.category_id')
      ->join('gen_mat_sub_category', 'stores_gen_pr_detail.sub_category_code', '=', 'gen_mat_sub_category.category_id')
      ->join('org_location', 'stores_gen_pr_header.location', '=', 'org_location.loc_id')    
      ->join('org_departments', 'stores_gen_pr_header.department', '=', 'org_departments.dep_id')  
      ->select('stores_gen_pr_header.*','gen_mat_sub_category.uom','org_departments.dep_name','org_location.loc_name', 'stores_gen_pr_detail.*','gen_mat_main_category.category_name',DB::raw('CONCAT(gen_mat_main_category.category_name," ",gen_mat_sub_category.category_code," ",gen_mat_sub_category.category_name) as itemDecs')) 
          ->where('stores_gen_pr_header.request_no'  , '=',$data['id'] )
      #->where('stores_gen_pr_header','like',$search.'%')
      /* ->orWhere('department'  , 'like', $search.'%' )
      ->orWhere('department'  , 'like', $search.'%' ) */
      //->orderBy($order_column, $order_type)
      ->offset($start)->limit($length)->get();

      $GeneralPR_count = GeneralPR::where('department'  , 'like', $search.'%' )
      ->orWhere('department'  , 'like', $search.'%' )
      ->orWhere('department'  , 'like', $search.'%' )
      ->count();

     // dd($GeneralPR_list);
      return [
          "draw" => $draw,
          "recordsTotal" => $GeneralPR_count,
          "recordsFiltered" => $GeneralPR_count,
          "data" => $GeneralPR_list
      ];
    }


    private function datatable_search_list($data)
    {
      $start = $data['start'];
      $length = $data['length'];
      $draw = $data['draw'];
      $search = $data['search']['value'];
      $order = $data['order'][0];
      $order_column = $data['columns'][$order['column']]['data'];
      $order_type = $order['dir'];

      $GeneralPR_list = GeneralPR::join('stores_gen_pr_detail', 'stores_gen_pr_header.request_no', '=', 'stores_gen_pr_detail.request_id')
      ->join('gen_mat_main_category', 'stores_gen_pr_detail.main_category', '=', 'gen_mat_main_category.category_id')
      ->join('gen_mat_sub_category', 'stores_gen_pr_detail.sub_category_code', '=', 'gen_mat_sub_category.category_id')
      ->join('org_location', 'stores_gen_pr_header.location', '=', 'org_location.loc_id')    
      ->join('org_departments', 'stores_gen_pr_header.department', '=', 'org_departments.dep_id')  
      ->select('stores_gen_pr_header.*','gen_mat_sub_category.uom','org_departments.dep_name','org_location.loc_name', 'stores_gen_pr_detail.*','gen_mat_main_category.category_name',DB::raw('CONCAT(gen_mat_main_category.category_name," ",gen_mat_sub_category.category_code," ",gen_mat_sub_category.category_name) as itemDecs')) 
       #->where('stores_gen_pr_header.request_no'  , '=',$data['id'] )
      #->where('stores_gen_pr_header','like',$search.'%')
      /* ->orWhere('department'  , 'like', $search.'%' )
      ->orWhere('department'  , 'like', $search.'%' ) */
      //->orderBy($order_column, $order_type)
      ->offset($start)->limit($length)->get();

      $GeneralPR_count = GeneralPR::where('department'  , 'like', $search.'%' )
      ->orWhere('department'  , 'like', $search.'%' )
      ->orWhere('department'  , 'like', $search.'%' )
      ->count();

     // dd($GeneralPR_list);
      return [
          "draw" => $draw,
          "recordsTotal" => $GeneralPR_count,
          "recordsFiltered" => $GeneralPR_count,
          "data" => $GeneralPR_list
      ];
    }
}
