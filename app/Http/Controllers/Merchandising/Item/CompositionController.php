<?php

namespace App\Http\Controllers\Merchandising\Item;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

use App\Models\Merchandising\Item\Composition;


class CompositionController extends Controller
{
    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => []]);
    }

    public function index(Request $request)
    {
        //$active = $request->active;
        //$fields = $request->fields;
        return response([
          'data' => $this->list()
        ]);
    }

    public function store(Request $request)
    {
      $compositions_type = new Composition();
      if($compositions_type->validate($request->all()))
      {
        $composition_count = Composition::where('content_description', '=', $request->content_description)->count();
        if($composition_count > 0){
          return response([
            'data' => [
              'status' => 'error',
              'message' => 'Fabric composition already exists.'
            ]
          ]);
        }
        else{
          $compositions_type->content_description = $request->content_description;
          $compositions_type->saveOrFail();
          return response([
            'data' => [
              'status' => 'success',
              'message' => 'Fabric composition saved successfully.'
            ]
          ]);
        }
      }
      else {
          $errors = $compositions_type->errors();// failure, get errors
          $errors_str = $compositions_type->errors_tostring();
          return response(['errors' => ['validationErrors' => $errors, 'validationErrorsText' => $errors_str]], Response::HTTP_UNPROCESSABLE_ENTITY);
      }
    }

    public function show($id)
    {
    }

    public function edit($id)
    {
    }

    public function update(Request $request, $id)
    {
    }

     //deactivate a item
     public function destroy($id)
     {
     }

     public function list(){
       return Composition::all();
     }

}
