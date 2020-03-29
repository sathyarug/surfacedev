<?php

namespace App\Http\Controllers\Merchandising\Item;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

use App\Models\Merchandising\Item\Category;
use App\Models\Merchandising\Item\ContentType;


class ContentTypeController extends Controller
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
      $content_type = new ContentType();
      if($content_type->validate($request->all()))
      {
        $type_description = strtoupper($request->type_description);
        if(ContentType::where('type_description', '=', $type_description)->count() > 0){
            return response([
              'data' => [
                'status' => 'error',
                'message' => 'Content type already exists'
              ]
            ]);
        }else{
            $content_type->type_description = $type_description;
            $content_type->saveOrFail();
            return response([
              'data' => [
                'status' => 'success',
                'message' => 'Content type saved successfully'
              ]
            ]);
        }
      }
      else {
          $errors = $order->errors();// failure, get errors
          return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
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
       return ContentType::all();
     }

}
