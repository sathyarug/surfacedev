<?php
namespace App\Http\Controllers\Org;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\CapitalizeAllFields;
use App\Http\Controllers\Controller;
use App\Models\Org\ConversionFactor;
use Exception;

class ConversionFactorController extends Controller
{
    public function __construct()
    {
      $this->middleware('jwt.verify', ['except' => ['index']]);
    }

    public function index(Request $request)
    {
      $type = $request->type;
      if($type == 'datatable')   {
        $data = $request->all();
        return response($this->datatable_search($data));
      }
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

      $data_list = ConversionFactor::select('*')   
      ->where('unit_code'  , 'like', $search.'%' )
      ->orWhere('description'  , 'like', $search.'%' )
      ->orWhere('base_unit'  , 'like', $search.'%' )
      ->orderBy('conv_id','ASC')
      ->offset($start)->limit($length)->get();

      $count = ConversionFactor::select('*')   
      ->where('unit_code'  , 'like', $search.'%' )
      ->orWhere('description'  , 'like', $search.'%' )
      ->orWhere('base_unit'  , 'like', $search.'%' )
      ->orderBy('conv_id','ASC')
      ->count();

      return [
          "draw" => $draw,
          "recordsTotal" => $count,
          "recordsFiltered" => $count,
          "data" => $data_list
      ];
    }

}
