<?php

namespace App\Http\Controllers\Finance\Cost;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use App\Models\Finance\Cost\FinanceCost;
use App\Models\Finance\Cost\FinanceCostHistory;



class FinanceCostController extends Controller
{
  public function __construct()
  {
    //add functions names to 'except' paramert to skip authentication
    $this->middleware('jwt.verify', ['except' => ['index']]);
  }


  //get SMVUpdate list
  public function index(Request $request)
  {
    $type = $request->type;
    if ($type == 'datatable') {
      $data = $request->all();
      return response($this->datatable_search($data));
    } else if ($type == 'auto') {
      $search = $request->search;
      return response($this->autocomplete_search($search));
    } else {
      return response([]);
    }
  }

  //create a Finance Cost
  public function store(Request $request)
  {

    $finCost = new FinanceCost();
    $finCostHis = new FinanceCostHistory();

    if ($finCost->validate($request->all()) && $finCostHis->validate($request->all())) {
      $finCost->fill($request->all());
      $finCost->status = 1;
      $finCost->save();

      $finCostHis->fill($request->all());
      $finCostHis->fin_cost_id = 1;
      $finCostHis->save();

      return response([
        'data' => [
          'message' => 'Finance Cost saved successfully',
          'finCost' => $finCost
        ]
      ], Response::HTTP_CREATED);
    } else {
      $errors = $finCost->errors(); // failure, get errors
      $errors_str = $finCost->errors_tostring();
      return response(['errors' => ['validationErrors' => $errors, 'validationErrorsText' => $errors_str]], Response::HTTP_UNPROCESSABLE_ENTITY);
    }
  }


  //get a finance Cost
  public function show($id)
  {

    // $finCost = FinanceCost::join('fin_fin_cost_his', 'fin_fin_cost.fin_cost_id', '=' , 'fin_fin_cost_his.fin_cost_id')
    // ->select('fin_fin_cost.*','fin_fin_cost_his.fin_cost_his_id')
    // ->where('fin_fin_cost_his.fin_cost_id',$id)->get();

    $finCost = FinanceCost::with(['history'])->find($id);


    if ($finCost == null)
      throw new ModelNotFoundException("Requested Finance Cost not found", 1);
    else
      return response(['data' => $finCost]);
  }


  //update a finance cost
  public function update(Request $request, $id)
  {
    //dd($request);
    $finCost = FinanceCost::find($id);
    $finCostHis = new FinanceCostHistory();
    if ($finCost->validate($request->all())) {
      $finCost->fill($request->all());

      $daystosum = '1';      

      //set values to history table
      $finCostHis->fin_cost_id = $finCost->fin_cost_id;
      $finCostHis->finance_cost = $finCost->finance_cost;
      $finCostHis->cpmfront_end = $finCost->cpmfront_end;
      $finCostHis->cpum = $finCost->cpum;

      $finCostHis->effective_from = date('Y-m-d', strtotime($finCost->effective_from . ' + ' . $daystosum . ' days'));
      $finCostHis->effective_to = date('Y-m-d', strtotime($finCost->effective_to . ' + ' . $daystosum . ' days'));
      //new effected from date
      // $effective_from_his = date_create($request->effective_from_);
      //new efffected from date assign to the old effetd to date by substrating a day
      // $effectiveTohis=$effective_from_his->modify("-1 day");
      // $splitTimeStamp = explode(" ",$effectiveTohis->format("Y-m-d H:i:s"));
      // $date = $splitTimeStamp[0];
      // $finCostHis->effective_to = date('Y-m-d',strtotime($date));

      $finCostHis->save();

      $effective_from = date_create($request->effective_from_);
      $finCost->effective_from = date_format($effective_from, "Y-m-d"); //change pcd date format to save in database

      $effective_to = date_create($request->effective_to_);
      $finCost->effective_to = date_format($effective_to, "Y-m-d"); //change pcd date format to save in database

      $finCost->save();

      return response(['data' => [
        'message' => 'Finance Cost updated successfully',
        'finCost' => $finCost
      ]]);
    } else {
      $errors = $finCost->errors(); // failure, get errors
      $errors_str = $finCost->errors_tostring();
      return response(['errors' => ['validationErrors' => $errors, 'validationErrorsText' => $errors_str]], Response::HTTP_UNPROCESSABLE_ENTITY);
    }
  }

  //get filtered fields only
  private function list($active = 0, $fields = null)
  {
    $query = null;
    if ($fields == null || $fields == '') {
      $query = FinanceCost::select('*');
    } else {
      $fields = explode(',', $fields);
      $query = FinanceCost::select($fields);
      if ($active != null && $active != '') {
        $query->where([['status', '=', $active]]);
      }
    }
    return $query->get();
  }

  //search UOM for autocomplete
  private function autocomplete_search($search)
  {
    $fin_cost_lists = FinanceCost::select('fin_cost_id', 'finance_cost')
      ->where([['finance_cost', 'like', '%' . $search . '%'],])->get();
    return $fin_cost_lists;
  }

  //get searched customers for datatable plugin format
  private function datatable_search($data)
  {
    //$start = $data['start'];
    //$length = $data['length'];
    $draw = $data['draw'];
    $search = $data['search']['value'];
    $order = $data['order'][0];
    $order_column = $data['columns'][$order['column']]['data'];
    $order_type = $order['dir'];

    $fin_cost_list = FinanceCost::select(DB::raw("DATE_FORMAT(effective_from, '%d-%b-%Y') 'from_date'"), DB::raw("DATE_FORMAT(effective_to, '%d-%b-%Y')'to_date'"), 'fin_cost_id', 'status', 'finance_cost', 'cpmfront_end', 'cpum', 'effective_from', 'effective_to')
      //->select('*')
      //$fin_cost_list=DB::table('fin_fin_cost')
      //->select(DB::raw('DATE_FORMAT("effective_from", "%d %b %Y")'))
      ->where('finance_cost', 'like', $search . '%')
      ->orderBy($order_column, $order_type)->get();
    //->offset($start)->limit($length)

    $fin_cost_count = FinanceCost::where('finance_cost', 'like', $search . '%')
      ->count();

    return [
      "draw" => $draw,
      "recordsTotal" => $fin_cost_count,
      "recordsFiltered" => $fin_cost_count,
      "data" => $fin_cost_list
    ];
  }
}
