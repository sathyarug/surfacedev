<?php

namespace App\Http\Controllers\Merchandising;

use App\Models\Merchandising\BOMHeader;
use App\Models\Merchandising\BOMDetails;
use App\Models\Merchandising\CustomerOrder;
use App\Models\Merchandising\CustomerOrderDetails;
use App\Models\Merchandising\Costing\Costing;

use App\Models\Merchandising\MaterialRatio;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class AppsController extends Controller
{
    public function index(Request $request)
    {
      $type = $request->type;
      /*if($type == 'header_data') {//return bom header data
        return response([
          'data' => $this->get_header_data($request->costing_id)
        ]);
      }  */
    }



    public function edit_mode(Request $request){
      $id = $request->id;
      $edit_status = $request->edit_status;
      $process = $request->process;

      $obj = $this->get_object($id, $process);
      $text = $this->get_process_text($process);
    //  echo $text;die();
      if($obj != null){ //has a costing
        if($edit_status == 1){//put to edit status
            $user_id = auth()->user()->user_id;

            if($obj->edit_status == 1 && $obj->created_by == $user_id){//already in edit mode
              return response([
                'status' => 'success',
                'message' => "You can edit " . $text
              ]);
            }
            else if($obj->edit_status == 1 && $obj->created_by != $user_id){
              return response([
                'status' => 'error',
                'message' => "You cannot edit " . $text. ". It's already in edit mode"
              ]);
            }
            else {
              if($obj->created_by == $user_id) {//costing created user and can edit
                $obj->edit_status = 1;
                $obj->edit_user = $user_id;
                $obj->save();
                return response([
                  'status' => 'success',
                  'message' => "You can edit " . $text
                ]);
              }
              else {
                return response([
                  'status' => 'error',
                  'message' => "Only " . $text. " created user can edit the " . $text
                ]);
              }
            }
        }
        else {//remove edit status
          $user_id = auth()->user()->user_id;
          if($obj->edit_status == 1 && $obj->edit_user == $user_id){//can edit
            $obj->edit_status = 0;
            $obj->edit_user = null;
            $obj->save();

            return response([
              'status' => 'success',
              'message' => ucfirst($text . " removed from edit status")
            ]);
          }
          else {
            return response([
              'status' => 'error',
              'message' => ucfirst($text . " is not in the edit status or user don't have permissions to edit costing")
            ]);
          }
        }
      }
      else {//no costing
        return response([
          'status' => 'error',
          'message' => "Incorrect " . $text
        ]);
      }
    }



    private function get_object($id, $process){
      if($process == 'COSTING'){
        return Costing::find($id);
      }
    }

    private function get_process_text($process){
      if($process == 'COSTING'){
        return 'costing';
      }
    }

}
