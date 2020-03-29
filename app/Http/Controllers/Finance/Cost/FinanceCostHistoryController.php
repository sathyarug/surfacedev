<?php

namespace App\Http\Controllers\Finance\Cost;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use App\Models\Finance\Cost\FinanceCostHistory;


class FinanceCostHistoryController extends Controller
{
    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => ['index']]);
    }

    //get finance cost history list
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
      else{
        return response([]);
      }
    }

    //create a finance cost
    public function store(Request $request)
    {
      $finCostHis = new FinanceCostHistory();
      if($finCostHis->validate($request->all()))
      {
        $finCostHis->fill($request->all());
        $finCostHis->fin_cost_id = 1;
        $finCostHis->save();

        return response([ 'data' => [
          'message' => 'Finance Cost History Updated successfully',
          'finCosthis' => $finCostHis
          ]
        ], Response::HTTP_CREATED );
      }
      else
      {
          $errors = $finCostHis->errors();// failure, get errors
          return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
      }
    }


    //get a finance Cost History
    public function show($id)
    {

      $finCostHis = FinanceCostHistory::find($id);

      if($finCostHis == null)
        throw new ModelNotFoundException("Requested Finance Cost not found", 1);
      else
        return response([ 'data' => $finCostHis ]);
    }
    //
    //
    //update a finance cost
    public function update(Request $request)
    {
      $finCostHis = new FinanceCostHistory();
      if($finCostHis->validate($request->all()))
      {
        $finCostHis->fill($request->all());

        $effective_from = date_create($request->effective_from_);

        $finCostHis->effective_from = date_format($effective_from,"Y-m-d");//change pcd date format to save in database

        $effective_to = date_create($request->effective_to_);
        $finCostHis->effective_to = date_format($effective_to,"Y-m-d");//change pcd date format to save in database

        $finCostHis->save();
        $hisId = $finCostHis->fin_cost_his_id-1;
        $oldEffectiveTo =$effective_from;
        //$effectiveTo = date($oldEffectiveTo,strtotime("-1 day"));
        $effectiveTo=$oldEffectiveTo->modify("-1 day");
        //$effectiveTo->format("Y-m-d H:i:s");
        //dd($effectiveTo->format("Y-m-d H:i:s"));
        $splitTimeStamp = explode(" ",$effectiveTo->format("Y-m-d H:i:s"));
        $date = $splitTimeStamp[0];
        //dd($date);
        $effectiveToNew = date('Y-m-d',strtotime($date));
      //dd($effectiveToNew);
        $finCosthis = FinanceCostHistory::find($hisId);
        if($finCosthis->validate($request->all()))
        {
          $finCosthis->fill($request->all());
          $finCosthis->effective_to=$effectiveToNew;
          $finCosthis->where('fin_cost_his_id','=',$hisId)->update(['effective_to' => $effectiveToNew]);
          //dd($finCosthis);
           //$finCosthis->save();

          // return response([ 'data' => [
          //   'message' => 'Finance Cost was updated successfully',
          //   'finCostHis' => $finCosthis
          // ]]);
        }

        return response([ 'data' => [
          'finCostHis' => $finCostHis
          // 'finCosthis' => $finCosthis
          ]
        ], Response::HTTP_CREATED );
      }
      else
      {
          $errors = $finCostHis->errors();// failure, get errors
          return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
      }

      // $hisId = FinanceCostHistory::select('fin_cost_his_id')
      //       ->orderBy('created_date','desc')
      //       ->groupBy('fin_cost_his_id')
      //       ->take(1)
      //       ->get();
      //
      // // dd($hisId);
      // $oldEffectiveTo = $request->effective_from;
      // $effectiveTo = $oldEffectiveTo('Y-m-d',strtotime("-1 days"));
      // $finCosthis = FinanceCostHistory::find($hisId);
      // if($finCosthis->validate($request->all()))
      // {
      //   $finCosthis->fill($request->all());
      //   $finCosthis->where('fin_cost_his_id','=',$hisId)->update(['effective_to' => $effectiveTo]);
      //   $finCosthis->save();
      //
      //   return response([ 'data' => [
      //     'message' => 'Finance Cost was updated successfully',
      //     'finCostHis' => $finCosthis
      //   ]]);
      // }
      // else
      // {
      //   $errors = $finCostHis->errors();// failure, get errors
      //   return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
      // }
    }



        //search finance cost for autocomplete
        private function autocomplete_search($search)
      	{
      		$fin_cost_his_lists = FinanceCostHistory::select('fin_cost_his_id','finance_cost')
      		->where([['finance_cost', 'like', '%' . $search . '%'],]) ->get();
      		return $fin_cost_his_lists;
      	}

    //get searched customers for datatable plugin format
    private function datatable_search($data)
    {
      $start = $data['start'];
      $length = $data['length'];
      $draw = $data['draw'];
      $search = $data['search']['value'];
      $order = $data['order'][0];
      $order_column = $data['columns'][$order['column']]['data'];
      $order_type = $order['dir'];

      $fin_cost_his_list = FinanceCostHistory::select( DB::raw("DATE_FORMAT(effective_from, '%d-%b-%Y') 'from_date'"),DB::raw("DATE_FORMAT(effective_to, '%d-%b-%Y')'to_date'"),'fin_cost_id','finance_cost','cpmfront_end','cpum','effective_from','effective_to')
      ->where('finance_cost'  , 'like', $search.'%' )
      //->orderBy($order_column, $order_type)
      ->orderBy('created_date','DESC')
      ->offset($start)->limit($length)->get();

      $fin_cost_his_count = FinanceCostHistory::where('finance_cost'  , 'like', $search.'%' )
      ->count();

      return [
          "draw" => $draw,
          "recordsTotal" => $fin_cost_his_count,
          "recordsFiltered" => $fin_cost_his_count,
          "data" => $fin_cost_his_list
      ];
    }



}
