<?php

namespace App\Http\Controllers\Merchandising\Costing;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Controllers\Controller;
use App\Models\Merchandising\Costing\CostingDesignSource;
use Exception;

class CostingDesignSourceController extends Controller
{

    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => ['index']]);    
    }

    //get Color list
    public function index(Request $request)
    {
        $type = $request->type;
        if($type == 'datatable') {
          /*  $data = $request->all();
            $this->datatable_search($data);*/
        }
        else if($type == 'auto') {
        /*  $search = $request->search;
          return response($this->autocomplete_search($search));*/
        }
        else {
          $active = $request->active;
          $fields = $request->fields;
          return response([
            'data' => $this->list($active , $fields)
          ]);
        }
    }


    //create a Color
    public function store(Request $request)
    {
      /*if($this->authorize->hasPermission('COLOR_MANAGE'))//check permission
      {
        //$capitalizeAllfields=new CapitalizeAllFields();
        $color = new Color();
        if($color->validate($request->all()))
        {
          $color->fill($request->all());

          $capitalizeAllFields=CapitalizeAllFields::setCapitalAll($color);
          $color->status = 1;
          //die();
          $color->save();

          return response([ 'data' => [
            'message' => 'Color saved successfully',
            'color' => $color,
            'status'=>1
            ]
          ], Response::HTTP_CREATED );
        }
        else
        {
            $errors = $color->errors();// failure, get errors
            return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
      }
      else {
        return response($this->authorize->error_response(), 401);
      }*/
    }


    //get a Color
    public function show($id)
    {
      /*if($this->authorize->hasPermission('COLOR_MANAGE'))//check permission
      {
        $color = Color::find($id);
        $color=Color::join('org_color_quality','org_color.col_quality','=','org_color.col_quality')
        ->where('org_color.color_id','=',$id)
        ->select('org_color.*','org_color_quality.color_quality_id','org_color_quality.col_quality')
        ->first();
        $quality=DB::table('org_color_quality')->select('*')->get();
        $color['quality']=$quality;

        if($color == null)
          throw new ModelNotFoundException("Requested color not found", 1);
        else
          return response([ 'data' => $color
                              ]);
      }
      else {
        return response($this->authorize->error_response(), 401);
      }*/
    }


    //update a Color
    public function update(Request $request, $id)
    {
      /*if($this->authorize->hasPermission('COLOR_MANAGE'))//check permission
      {

        $color = Color::find($id);
        if($color->validate($request->all()))
        {
          $is_exists_costing_finish_goods = DB::table('costing_finish_good_components')->where('color_id', $id)->exists();
          $is_exists_costing_goods = DB::table('costing_finish_goods')->where('combo_color_id', $id)->exists();
          //$is_exists = DB::table('costing_bulk_details')->where('color_id', $id)->exists();
          $is_exsits_cus_po=DB::table('merc_customer_order_details')->where('style_color', $id)->exists();
          if($is_exists_costing_finish_goods==true||$is_exists_costing_goods==true||/*$is_exists==true||$is_exsits_cus_po==true){
            return response([ 'data' => [
              'message' => 'Color Already in Use',
              'status' =>0
            ]]);
          }
          else{

          $color->fill($request->except('color_code'));
          $capitalizeAllFields=CapitalizeAllFields::setCapitalAll($color);
          $color->save();

          return response([ 'data' => [
            'message' => 'Color updated successfully',
            'color' => $color,
            'status'=>1
          ]]);
        }
        }
        else
        {
          $errors = $color->errors();// failure, get errors
          return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
      }
      else {
        return response($this->authorize->error_response(), 401);
      }*/

    }


    //deactivate a Color
    public function destroy($id)
    {
      /*if($this->authorize->hasPermission('COLOR_DELETE'))//check permission
      {
        $is_exists_costing_finish_goods = DB::table('costing_finish_good_components')->where('color_id', $id)->exists();
        $is_exists_costing_goods = DB::table('costing_finish_goods')->where('combo_color_id', $id)->exists();
        //$is_exists = DB::table('costing_bulk_details')->where('color_id', $id)->exists();
        $is_exsits_cus_po=DB::table('merc_customer_order_details')->where('style_color', $id)->exists();
        if($is_exists_costing_finish_goods==true||$is_exists_costing_goods==true||$is_exsits_cus_po==true){
          return response([
            'data' => [
              'message' => 'Color Already in Use',
              'status'=>0,
            ]
          ]);
        }

        $color = Color::where('color_id', $id)->update(['status' => 0]);
        return response([
          'data' => [
            'message' => 'Color was deactivated successfully.',
            'color' => $color,
            'status'=>1
          ]
        ]);
      }
      else {
        return response($this->authorize->error_response(), 403);
      }*/
    }


    //validate anything based on requirements
    public function validate_data(Request $request){
    /*  $for = $request->for;
      if($for == 'duplicate') {
        return response($this->validate_duplicate_code($request->color_id , $request->color_code));
      }
    */
    }


    //check Color code already exists
    private function validate_duplicate_code($id, $code/*,$colorName,$colorCategory,$colorQuality*/)
    {
      //$color = Color::where([['color_code','=',$code]/*,['color_name','=',$colorName],['color_category','=',$colorCategory],['col_quality','=',$colorQuality]*/])->first();
      /*if($color == null){
        return ['status' => 'success'];
      }
      else if($color->color_id == $id){
        return ['status' => 'success'];
      }
      else {
        return ['status' => 'error','message' => 'Color code already exists'];
      }*/
    }


    //get filtered fields only
    private function list($active = 0 , $fields = null)
    {
        $query = null;
        if($fields == null || $fields == '') {
          $query = CostingDesignSource::select('*');
        }
        else{
          $fields = explode(',', $fields);
          $query = CostingDesignSource::select($fields);
          if($active != null && $active != ''){
            $query->where([['status', '=', $active]]);
          }
        }
        return $query->get();
    }



    //search Color for autocomplete
    private function autocomplete_search($search)
  	{
  		/*$color_lists = Color::select('color_id','color_name')
  		->where([['color_name', 'like', '%' . $search . '%'],]) ->get();
  		return $color_lists;*/
  	}

    //get searched Colors for datatable plugin format
    private function datatable_search($data)
    {
      /*if($this->authorize->hasPermission('COLOR_MANAGE') == true){//check permission

          $start = $data['start'];
          $length = $data['length'];
          $draw = $data['draw'];
          $search = $data['search']['value'];
          $order = $data['order'][0];
          $order_column = $data['columns'][$order['column']]['data'];
          $order_type = $order['dir'];

          $color_list = Color::join('org_color_category','org_color.color_category','=','org_color_category.color_category_id')

          ->select('org_color.*','org_color_category.color_category')
          ->where('org_color.color_code'  , 'like', $search.'%' )
          ->orWhere('org_color.color_name'  , 'like', $search.'%' )
          ->orWhere('org_color_category.color_category','like',$search.'%')
          ->orWhere('org_color.col_quality','like',$search.'%')
          ->orderBy($order_column, $order_type)
          ->offset($start)->limit($length)->get();

          $color_count = Color::join('org_color_category','org_color.color_category','=','org_color_category.color_category_id')
          ->where('org_color.color_code'  , 'like', $search.'%' )
          ->orWhere('org_color.color_name'  , 'like', $search.'%' )
          ->orWhere('org_color_category.color_category','like',$search.'%')
          ->orWhere('org_color_category.color_category','like',$search.'%')
          ->orWhere('org_color.col_quality','like',$search.'%')
          ->count();

          echo json_encode([
              "draw" => $draw,
              "recordsTotal" => $color_count,
              "recordsFiltered" => $color_count,
              "data" => $color_list
          ]);
      }
      else{
        return response($this->authorize->error_response(), 401);
      }*/
    }

}
