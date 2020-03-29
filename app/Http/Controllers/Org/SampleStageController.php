<?php

namespace App\Http\Controllers\Org;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Org\SampleStage;
class SampleStageController extends Controller
{
  public function index() {
        return view('sample_stage.sample_stage');
    }

    public function loadData() {
        $sample_list = SampleStage::all();
        echo json_encode($sample_list);
    }

    public function checkCode(Request $request) {
        $count = SampleStage::where('sample_code', '=', $request->code)->count();

        if ($request->idcode > 0) {

            $user = SampleStage::where('sample_id', $request->idcode)->first();

            if ($user->sample_code == $request->code) {
                $msg = true;
            } else {

                $msg = 'Already exists. please try another one';
            }
        } else {

            if ($count == 1) {

                $msg = 'Already exists. please try another one';
            } else {

                $msg = true;
            }
        }
        echo json_encode($msg);
    }

    public function saveStage(Request $request) {
        $sample = new SampleStage();
        if ($sample->validate($request->all())) {
            if ($request->sample_hid > 0) {
                $sample = SampleStage::find($request->sample_hid);
                $sample->sample_description=$request->sample_description;
            } else {
                $sample->fill($request->all());
                $sample->status = 1;
                $sample->created_by = 1;
            }
            $sample = $sample->saveOrFail();
            // echo json_encode(array('Saved'));
            echo json_encode(array('status' => 'success', 'message' => 'Sample stage details saved successfully.'));
        } else {
            // failure, get errors
            $errors = $sample->errors();
            echo json_encode(array('status' => 'error', 'message' => $errors));
        }
    }

    public function edit(Request $request) {
        $sample_id = $request->sample_id;
        $sample = SampleStage::find($sample_id);
        echo json_encode($sample);
    }

    public function delete(Request $request) {
        $sample_id = $request->sample_id;
        //$source = Main_Source::find($source_id);
        //$source->delete();
        $sample = SampleStage::where('sample_id', $sample_id)->update(['status' => 0]);
        echo json_encode(array('delete'));
    }
}
