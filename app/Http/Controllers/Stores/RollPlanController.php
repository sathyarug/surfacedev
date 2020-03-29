<?php

namespace App\Http\Controllers\stores;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\stores\PoOrderDetails;
use App\Models\stores\PoOrderHeader;
use App\Models\stores\PoOrderType;
use App\Models\stores\StoRollDescription;
use DB;

class RollPlanController extends Controller
{
    public function index(Request $request)
    {


        $type = $request['type'];
        if($type == 'datatable'){
            $draw = $request['draw'];
            $po = $request['text'];
            return $this->dataTable($draw,$po);
        }elseif ($type == 'loadPo'){
            $poId = $request['poId'];
            return $this->loadPo($poId);
        }

    }

    private function dataTable($draw,$po)
    {
        $data=DB::table('po_order_details')
            ->join('po_order_header', 'po_order_details.po_id', '=', 'po_order_header.po_id')
            ->join('po_order_type', 'po_order_header.po_status', '=', 'po_order_type.po_type_id')
            ->select('*')
            ->where('po_order_type.po_status_name','<>','CANCEL')
            ->where('po_order_header.po_id','=',$po)
            ->get();

        return json_encode([
            "draw" => $draw,
            "recordsTotal" => 1,
            "recordsFiltered" =>1,
            "data" =>  $data
        ]);
    }

    private function loadPo($poId)
    {
        $dataForPo=DB::table('po_order_details')
            ->join('po_order_header', 'po_order_details.po_id', '=', 'po_order_header.po_id')
            ->join('po_order_type', 'po_order_header.po_status', '=', 'po_order_type.po_type_id')
            ->select('*')
            ->where('po_order_type.po_status_name','<>','CANCEL')
            ->where('po_order_header.po_id','=',$poId)
            ->get();

        $dataForRoll=DB::table('po_order_details')
            ->join('po_order_header', 'po_order_details.po_id', '=', 'po_order_header.po_id')
            ->join('po_order_type', 'po_order_header.po_status', '=', 'po_order_type.po_type_id')
            ->join('sto_roll_description', 'sto_roll_description.item_code', '=', 'po_order_details.item_code')
            ->select('*')
            ->where('po_order_type.po_status_name','<>','CANCEL')
            ->where('sto_roll_description.status','=',1)
            ->where('po_order_header.po_id','=',$poId)
            ->get();

        return array('po'=>$dataForPo,'roll'=>$dataForRoll);
    }

    public function store(Request $request)
    {
        $itemCode = $request['code'];
       // $itemCode=($request->all())['roll_info'][0]['item_code'];

        StoRollDescription::where('item_code', '=', $itemCode)->update(['status' => 0]);

        if(count($request->all()['roll_info']) > 0){



            $saveSuccess=0;$error=0;
            $updateSuccess=0;

            foreach ($request->all()['roll_info'] as $key => $value)
            {

                if($value['roll_id'] ==0) {
                    $rollDes = new StoRollDescription();
                    $fill = array(
                        'item_code' => $value['item_code'],
                        'lot_no' => $value['lot'],
                        'roll_no' => $value['roll'],
                        'qty_yardage' => $value['qty_y'],
                        'comment' => $value['comment']
                    );

                    if ($rollDes->validate($fill)) {
                        $rollDes->fill($fill);
                        $rollDes->save();
                        $saveSuccess++;
                    } else {
                        $error++;
                        // $errors = $rollDes->errors();// failure, get errors
                        // return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
                    }

                }else{
                    $rollDes = StoRollDescription::find($value['roll_id']);

                    $fill = array(
                        'item_code' => $value['item_code'],
                        'lot_no' => $value['lot'],
                        'roll_no' => $value['roll'],
                        'qty_yardage' => $value['qty_y'],
                        'status' => 1,
                        'comment' => $value['comment']
                    );

                    if ($rollDes->validate($fill)) {
//               dd($fill);
//               $rollDes->fill($fill);
                        $rollDes->item_code=$value['item_code'];
                        $rollDes->lot_no=$value['lot'];
                        $rollDes->roll_no=$value['roll'];
                        $rollDes->qty_yardage=$value['qty_y'];
                        $rollDes->status=1;
                        $rollDes->comment=$value['comment'];

                        $rollDes->save();
                        $updateSuccess++;
                    } else {
                        $error++;
//               $errors = $rollDes->errors();
//               return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
                    }

                }
            }

            return array('save'=>array('save'=>$saveSuccess,'error'=>$error),'update'=>array('save'=>$saveSuccess));

        }else{

        }
      }

    private function loadPo($poId)
    {
        $dataForPo=DB::table('po_order_details')
            ->join('po_order_header', 'po_order_details.po_id', '=', 'po_order_header.po_id')
            ->join('po_order_type', 'po_order_header.po_status', '=', 'po_order_type.po_type_id')
            ->select('*')
            ->where('po_order_type.po_status_name','<>','CANCEL')
            ->where('po_order_header.po_id','=',$poId)
            ->get();

        $dataForRoll=DB::table('po_order_details')
            ->join('po_order_header', 'po_order_details.po_id', '=', 'po_order_header.po_id')
            ->join('po_order_type', 'po_order_header.po_status', '=', 'po_order_type.po_type_id')
            ->join('sto_roll_description', 'sto_roll_description.item_code', '=', 'po_order_details.item_code')
            ->select('*')
            ->where('po_order_type.po_status_name','<>','CANCEL')
            ->where('sto_roll_description.status','=',1)
            ->where('po_order_header.po_id','=',$poId)
            ->get();

        return array('po'=>$dataForPo,'roll'=>$dataForRoll);
    }

    public function store(Request $request)
    {
        $itemCode=($request->all())['roll_info'][0]['item_code'];

        StoRollDescription::where('item_code', '=', $itemCode)->update(['status' => 0]);

        $saveSuccess=0;$error=0;
        $updateSuccess=0;

        foreach ($request->all()['roll_info'] as $key => $value)
        {

       if($value['roll_id'] ==0) {
           $rollDes = new StoRollDescription();
           $fill = array(
               'item_code' => $value['item_code'],
               'lot_no' => $value['lot'],
               'roll_no' => $value['roll'],
               'qty_yardage' => $value['qty_y'],
               'comment' => $value['comment']
           );

           if ($rollDes->validate($fill)) {
               $rollDes->fill($fill);
                $rollDes->save();
               $saveSuccess++;
           } else {
               $error++;
              // $errors = $rollDes->errors();// failure, get errors
              // return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
           }

       }else{
           $rollDes = StoRollDescription::find($value['roll_id']);

           $fill = array(
               'item_code' => $value['item_code'],
               'lot_no' => $value['lot'],
               'roll_no' => $value['roll'],
               'qty_yardage' => $value['qty_y'],
               'status' => 1,
               'comment' => $value['comment']
           );

           if ($rollDes->validate($fill)) {
//               dd($fill);
//               $rollDes->fill($fill);
               $rollDes->item_code=$value['item_code'];
               $rollDes->lot_no=$value['lot'];
               $rollDes->roll_no=$value['roll'];
               $rollDes->qty_yardage=$value['qty_y'];
               $rollDes->status=1;
               $rollDes->comment=$value['comment'];

               $rollDes->save();
               $updateSuccess++;
           } else {
               $error++;
//               $errors = $rollDes->errors();
//               return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
           }

       }
        }

        return array('save'=>array('save'=>$saveSuccess,'error'=>$error),'update'=>array('save'=>$saveSuccess));

    }
}
