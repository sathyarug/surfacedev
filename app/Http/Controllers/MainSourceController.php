<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Main_Source;
use App\Http\Controllers\Controller;

class MainSourceController extends Controller
{



  public function postdata(Request $request)
   {
        $main_source = new Main_Source();       
        if ($main_source->validate($request->all()))   
        {
            if($request->source_hid > 0){
                $main_source = Main_Source::find($request->source_hid);
            }     
            $main_source->fill($request->all());
            $main_source->status = 1;
            $main_source->created_by = 1;  
            $result = $main_source->saveOrFail();
           // echo json_encode(array('Saved'));
            if($result)
              {
                  return response()->json(["message"=>$result]);
              }
        }
        else
        {            
            // failure, get errors
            $errors = $main_source->errors();
            print_r($errors);
        }        


   }

   public function loaddata()
   {
   			 $source_list = Main_Source::all();
       		 echo json_encode($source_list);

   }

   public function check_code(Request $request)
   {


   			$count = Main_Source::where('source_code','=',$request->code)->count();

        if($request->idcode > 0){

          $user = Main_Source::where('source_id', $request->idcode)->first();

              if($user->source_code == $request->code)
              {
                  $msg = true;

              }else{

                  $msg = 'Already exists. please try another one';

              }


        }else{

            if($count == 1){ 

                  $msg = 'Already exists. please try another one'; 

              }else{ 

                  $msg = true; 
                  
              }

        }
   			echo json_encode($msg);
   }


   public function edit(Request $request)
   {
   		$source_id = $request->source_id;
        $source = Main_Source::find($source_id);
        echo json_encode($source);
   			
   }


   public function delete(Request $request)
   {
        $source_id = $request->source_id;
        //$source = Main_Source::find($source_id);
        //$source->delete();
        $source = Main_Source::where('source_id', $source_id)->update(['status' => 0]);
        echo json_encode(array('delete'));
   }


   public function select_Source_list(){

    $s_source_lists = Main_Source::select('source_code','source_name')->get();

    return response()->json(['items'=>$s_source_lists]);
    //return $select_source;

  }



}
