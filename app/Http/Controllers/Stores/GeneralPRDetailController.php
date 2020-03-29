<?php

namespace App\Http\Controllers\Stores;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\stores\GeneralPRDetail;

class GeneralPRDetailController  extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        dd('sdsd');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        dd('CC');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
         $generalPRdetail = new GeneralPRDetail();
        // dd($request->all());
      if($generalPRdetail->validate($request->all()))
      {
      
        $generalPRdetail->fill($request->all());
        $generalPRdetail->status = 1;
        //$generalPRdetail->user_id = 9;
         //dd ($request->all());
        $generalPRdetail->save();
       
        return response([ 'data' => [
          'message' => 'generalPRdetail was saved successfully',
          'generalPRdetail' => $generalPRdetail
          ]
        ], Response::HTTP_CREATED );
      }
      else
      {
          $errors = $generalPRdetail->errors();// failure, get errors
          return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
      }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        dd('GG');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        dd('XX');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        dd('ADd');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $source = GeneralPRDetail::where('id', $id)->update(['status' => 0]);
      return response([
        'data' => [
          'message' => 'Source was deactivated successfully.',
          'source' => $source
        ]
      ] , Response::HTTP_NO_CONTENT);
    }
}
