<?php

namespace App\Http\Controllers\Org\Store;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Org\Store\StoreBin;

class StoreBinController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $type = $request->type;
        //dd($type);
        if ($type == 'datatable') {
            $data = $request->all();
            return response($this->datatable_search($data));
        } else if ($type == 'auto') {
            $search = $request->search;
            return response($this->autocomplete_search($search));
        }
           else {
            $active = $request->active;
            $fields = $request->fields;
            return response([
                'data' => $this->list($active, $fields)
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $storeBin = new StoreBin();
        if ($storeBin->validate($request->all())) {
            $storeBin->fill($request->all());
            $storeBin->status = 1;
            $storeBin->save();

            return response(['data' => [
                    'message' => 'Store bin saved successfully',
                    'storeBin' => $storeBin
                ]
                    ], Response::HTTP_CREATED);
        } else {
          $errors = $storeBin->errors();// failure, get errors
          $errors_str = $storeBin->errors_tostring();
          return response(['errors' => ['validationErrors' => $errors, 'validationErrorsText' => $errors_str]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $storeBin = StoreBin::find($id);
        if ($storeBin == null)
            throw new ModelNotFoundException("Requested store not found", 1);
        else
            return response(['data' => $storeBin]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $storeBin = StoreBin::find($id);
        if ($storeBin->validate($request->all())) {
            $storeBin->fill($request->except('store_bin_name'));
            $storeBin->save();

            return response(['data' => [
                    'message' => 'Store was updated successfully',
                    'storeBin' => $storeBin
            ]]);
        } else {
          $errors = $storeBin->errors();// failure, get errors
          $errors_str = $storeBin->errors_tostring();
          return response(['errors' => ['validationErrors' => $errors, 'validationErrorsText' => $errors_str]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $storeBin = StoreBin::where('store_bin_id', $id)->update(['status' => 0]);
        return response([
            'data' => [
                'message' => 'Store was deactivated successfully.',
                'store' => $storeBin
            ]
                ], Response::HTTP_NO_CONTENT);
    }

    //get filtered fields only
    private function list($active = 0, $fields = null) {
        $query = null;
        if ($fields == null || $fields == '') {
            $query = StoreBin::select('*');
        } else {
            $fields = explode(',', $fields);
            $query = StoreBin::select($fields);
            if ($active != null && $active != '') {
                $query->where([['status', '=', $active]]);
            }
        }
        return $query->get();
    }

    //search goods types for autocomplete
    private function autocomplete_search($search) {
        $bin_list = StoreBin::select('store_bin_id', 'store_bin_name')
                        ->where([['store_bin_name', 'like', '%' . $search . '%'],])->get();
        return $bin_list;
    }

    //search bin  related to the subStoreBin
  /*  private function autocomplete_substore_wise_bin_search($search,$subStoreBin) {
      //dd("dadadada");
        $bin_list = DB::table('org_store_bin')->select('store_bin_name')
                        ->where([['store_bin_name', 'like', '%' . $search . '%'],])
                        ->where('substore_id','=',$subStoreBin)->get();
        return $bin_list;
    }
*/
    //get searched goods types for datatable plugin format
    private function datatable_search($data) {
        $start = $data['start'];
        $length = $data['length'];
        $draw = $data['draw'];
        $search = $data['search']['value'];
        $order = $data['order'][0];
        $order_column = $data['columns'][$order['column']]['data'];
        $order_type = $order['dir'];

        $bin_list = StoreBin::select('*')
                        ->where('store_bin_name', 'like', $search . '%')
                        ->orderBy($order_column, $order_type)
                        ->offset($start)->limit($length)->get();

        $bin_count = StoreBin::where('store_bin_name', 'like', $search . '%')
                ->count();

        return [
            "draw" => $draw,
            "recordsTotal" => $bin_count,
            "recordsFiltered" => $bin_count,
            "data" => $bin_list
        ];
    }

    //validate anything based on requirements
    public function validate_data(Request $request) {
        $for = $request->for;
        if ($for == 'duplicate') {
            return response($this->validate_duplicate_bin($request->store_bin_id, $request->store_bin_name));
        }
    }

    //check shipment cterm code code already exists
    private function validate_duplicate_bin($id, $name) {
        $bin = StoreBin::where('store_bin_name', '=', $name)->first();
        if ($bin == null) {
            return ['status' => 'success'];
        } else if ($bin->store_bin_id == $id) {
            return ['status' => 'success'];
        } else {
            return ['status' => 'error', 'message' => 'Bin already exists'];
        }
    }

}
