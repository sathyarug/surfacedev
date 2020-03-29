<?php

namespace App\Http\Controllers\Org;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Org\ProductType;

class ProductTypeController extends Controller
{
    public function index() {
        return view('product_type.product_type');
    }

    public function loadData() {
        $product_list = ProductType::all();
        echo json_encode($product_list);
    }

    public function checkCode(Request $request) {
        $count = ProductType::where('product_code', '=', $request->code)->count();

        if ($request->idcode > 0) {

            $user = ProductType::where('product_id', $request->idcode)->first();

            if ($user->product_code == $request->code) {
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

    public function saveProduct(Request $request) {
        $product = new ProductType();
        if ($product->validate($request->all())) {
            if ($request->product_hid > 0) {
                $product = ProductType::find($request->product_hid);
                $product->product_description=$request->product_description;
            } else {
                $product->fill($request->all());
                $product->status = 1;
                $product->created_by = 1;
            }
            $product = $product->saveOrFail();
            // echo json_encode(array('Saved'));
            echo json_encode(array('status' => 'success', 'message' => 'Product type details saved successfully.'));
        } else {
            // failure, get errors
            $errors = $product->errors();
            echo json_encode(array('status' => 'error', 'message' => $errors));
        }
    }

    public function edit(Request $request) {
        $product_id = $request->product_id;
        $product = ProductType::find($product_id);
        echo json_encode($product);
    }

    public function delete(Request $request) {
        $product_id = $request->product_id;
        //$source = Main_Source::find($source_id);
        //$source->delete();
        $product = ProductType::where('product_id', $product_id)->update(['status' => 0]);
        echo json_encode(array('delete'));
    }
}
