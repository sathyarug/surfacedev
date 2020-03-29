<?php
namespace App\Http\Controllers\Org;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\CapitalizeAllFields;
use App\Http\Controllers\Controller;
use App\Models\Org\SizeChart;
use App\Models\Org\SizeChartSizes;
use Exception;

class SizeChartController extends Controller
{
    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => ['index']]);
    }

    //get component list
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
      else if($type == 'chart_sizes'){
        $size_chart_id = $request->size_chart_id;
        return response([
          'data' => $this->load_saved_sizes($size_chart_id)
        ]);
      }
      else {
        $active = $request->active;
        $fields = $request->fields;
        return response([
          'data' => $this->list($active , $fields)
        ]);
      }
    }

    //deactivate a garment component
    public function destroy($id)
    {
      $delete = SizeChart::where('size_chart_id', $id)->update(['status' => 0]);
      return response([
        'data' => [
          'message' => 'Size Chart was deactivated successfully.',
          'delete' => $delete
        ]
      ] , Response::HTTP_NO_CONTENT);
    }

    //validate anything based on requirements
    public function validate_data(Request $request){
      $for = $request->for;
      if($for == 'duplicate')
      {
        return response($this->validate_duplicate($request->size_chart_id,$request->description));
      }
    }

    private function validate_duplicate($id,$description)
    {
      $comb = SizeChart::where([['chart_name','=',$description]])->first();
      if($comb == null){
        return ['status' => 'success'];
      }
      else if($comb->size_chart_id == $id){
        return ['status' => 'success'];
      }
      else {
        return ['status' => 'error','message' => 'Description was already exists'];
      }
    }

    public function store(Request $request)
    {
      $chart_name = "";
      foreach($request['size_name'] as $row){
        $chart_name .= $row['size_name'] . ",";
      }

      $data = array("size_chart_id"=>$request->size_chart_id, "description"=>$chart_name, "chart_name"=>$request->description);

      $SizeChartHeader = new SizeChart();
      if($SizeChartHeader->validate($data)){

        $SizeChartHeader->chart_name = $request->description;
        $SizeChartHeader->description = $chart_name;
        $SizeChartHeader->status = 1;
        CapitalizeAllFields::setCapitalAll($SizeChartHeader);
        $SizeChartHeader->save();
        $size_chart_id = $SizeChartHeader->size_chart_id;

        if($SizeChartHeader){
             foreach($request['size_name'] as $row){
                $SizeChartDetails = new SizeChartSizes();
                $SizeChartDetails->size_chart_id = $size_chart_id;
                $SizeChartDetails->size_id = $row['size_id'];
                $SizeChartDetails->status = 1;
                $SizeChartDetails->save();
             }
             return response([ 'data' => [
                'result' => 'insert',
                'message' => 'Size chart saved successfully'
               ]
             ], Response::HTTP_CREATED );
          }
          else
          {
              $errors = $SizeChartHeader->errors();
              return response([ 'data' => [
                  'result' => $errors,
                  'message' => 'Size chart saved fail'
                ]
              ], Response::HTTP_CREATED );
          }

      }else{
        $errors = $SizeChartHeader->errors();// failure, get errors
        $errors_str = $SizeChartHeader->errors_tostring();
        return response(['errors' => ['validationErrors' => $errors, 'validationErrorsText' => $errors_str]], Response::HTTP_UNPROCESSABLE_ENTITY);
      }

    }

    //Find values of to be edited row
    public function show($id)
    {
        $SizeChart = SizeChart::select('*')
        ->where('size_chart_id' ,'=', $id)
        ->get();
        $rows = array();
        foreach($SizeChart as $row)
        {
          $row['sizes'] = $this->load_saved_sizes($row->size_chart_id);
          $rows[] = $row;
        }
        if($SizeChart == null)
          throw new ModelNotFoundException("Requested component not found", 1);
        else
          return response([ 'data' => $SizeChart ]);

    }

    public function load_saved_sizes($id)
    {
      $data = SizeChartSizes::join('org_size','org_size_chart_sizes.size_id','=','org_size.size_id')
      ->select('org_size_chart_sizes.size_id','size_name')
      ->where('org_size_chart_sizes.size_chart_id' ,'=', $id)
      ->orderBy('size_name','ASC')
      ->get();
      return $data;
    }

    //update data
    public function update(Request $request, $id)
    {

      $chart_name = "";
      foreach($request['size_name'] as $row){
        $chart_name .= $row['size_name'] . ",";
      }

      $update = SizeChart::where('size_chart_id','=',$request->size_chart_id)
      ->update(
          [
            'chart_name' => strtoupper($request->description),
            'description' => $chart_name,
            'status' => 1,
            'updated_by' => auth()->payload()['user_id']
          ]
      );

      $delete = SizeChartSizes::where('size_chart_id','=',$request->size_chart_id)->delete();

      if($update){
           foreach($request['size_name'] as $row){
              $SizeChartDetails = new SizeChartSizes();
              $SizeChartDetails->size_chart_id = $request->size_chart_id;
              $SizeChartDetails->size_id = $row['size_id'];
              $SizeChartDetails->status = 1;
              $SizeChartDetails->save();
           }
           return response([ 'data' => [
              'result' => 'update',
              'message' => 'Size chart updated successfully'
             ]
           ], Response::HTTP_CREATED );
        }
        else
        {
            $errors = $update->errors();
            return response([ 'data' => [
                'result' => $errors,
                'message' => 'Data update fail'
              ]
            ], Response::HTTP_CREATED );
        }

    }


    //get filtered fields only
    private function list($active = 0 , $fields = null)
    {
        $query = null;
        if($fields == null || $fields == '') {
          $query = SizeChart::select('*');
        }
        else{
          $fields = explode(',', $fields);
          $query = Color::select($fields);
        }

        if($active != null && $active != ''){
          $query->where([['status', '=', $active]]);
        }
        return $query->get();
     }


    private function autocomplete_search($search)
   	{
   		$size_lists = SizeChart::select('size_chart_id', 'chart_name', 'description')
   		->where([['chart_name', 'like', '%' . $search . '%'],]) ->get();
   		return $size_lists;
   	}

    //get searched data for datatable plugin format
    private function datatable_search($data)
    {

      $start = $data['start'];
      $length = $data['length'];
      $draw = $data['draw'];
      $search = $data['search']['value'];
      $order = $data['order'][0];
      $order_column = $data['columns'][$order['column']]['data'];
      $order_type = $order['dir'];

      $size_list = SizeChart::select('*')
      ->where('chart_name'  , 'like', $search.'%' )
      ->orWhere('description'  , 'like', $search.'%' )
      ->orderBy($order_column, $order_type)
      ->offset($start)->limit($length)->get();

      $count = SizeChart::select('*')
      ->where('chart_name'  , 'like', $search.'%' )
      ->orWhere('description'  , 'like', $search.'%' )
      ->orderBy('chart_name','ASC')
      ->count();

      return [
          "draw" => $draw,
          "recordsTotal" => $count,
          "recordsFiltered" => $count,
          "data" => $size_list
      ];
    }

}
