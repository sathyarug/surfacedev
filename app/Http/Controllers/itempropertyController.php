<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\itemproperty;
use Illuminate\Http\Request;

use App\Models\Finance\Item\Category;
use App\Models\Finance\Item\SubCategory;
use App\assign_property;

class itempropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $keyword = $request->get('search');
        $perPage = 25;

        if (!empty($keyword)) {
            $itemproperty = itemproperty::where('property_id', 'LIKE', "%$keyword%")
                ->latest()->paginate($perPage);
        } else {
            $itemproperty = itemproperty::latest()->paginate($perPage);
        }

        $data = array(
          'categories' => Category::all()
        );

        //return view('itemproperty.itemproperty', compact('itemproperty',$data));
        return view('itemproperty.itemproperty',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('itemproperty.itemproperty.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {

        $requestData = $request->all();

        itemproperty::create($requestData);

        return redirect('itemproperty')->with('flash_message', 'itemproperty added!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $itemproperty = itemproperty::findOrFail($id);

        return view('itemproperty.itemproperty.show', compact('itemproperty'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $itemproperty = itemproperty::findOrFail($id);

        return view('itemproperty.itemproperty.edit', compact('itemproperty'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, $id)
    {

        $requestData = $request->all();

        $itemproperty = itemproperty::findOrFail($id);
        $itemproperty->update($requestData);

        return redirect('itemproperty')->with('flash_message', 'itemproperty updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id)
    {
        itemproperty::destroy($id);

        return redirect('itemproperty')->with('flash_message', 'itemproperty deleted!');
    }

    public function SaveItemProperty(Request $request){

        $item_properties = new itemproperty();

        $item_properties->property_name = $request->property_name;
        $item_properties->status = 1;
        $item_properties->saveOrFail();

         echo json_encode(array('status' => 'success'));
    }

    public function LoadProperties(){

        $item_property = itemproperty::where('status','=','1')->pluck('property_id','property_name');

        echo json_encode($item_property);
    }

    public function RemoveAssign(Request $request){

        $propperty_assign = new assign_property();

        echo json_encode("Code : ".$request->sub_code);

        $propperty_assign::where('subcategory_id',$request->sub_code)->delete();


    }

    public function SavePropertyAssign(Request $request){

        $propperty_assign = new assign_property();


        $obj = assign_property::where('property_id',$request->property_id)->where('subcategory_id',$request->subcategory_code);

        if($obj->count()>0){
             $obj->sequence_no = $request->sequence_no;
             $obj->save();

        }else{

            $propperty_assign->property_id = $request->property_id;
            $propperty_assign->subcategory_id = $request->subcategory_code;
            $propperty_assign->status = 1;
            $propperty_assign->sequence_no = $request->sequence_no;

            $propperty_assign->saveOrFail();

        }

        echo json_encode(array('status' => 'success'));
    }

    public function LoadAssignProperties(Request $request){

        $propperty_assign = new itemproperty();
        $obj = $propperty_assign->LoadAssignProperties($request);

        echo json_encode($obj);

    }

    public function LoadUnAssignPropertiesBySubCat(Request $request){
        $propperty_assign = new itemproperty();

        $subcatcode = $request->subcategory_code;
        $objUnassignPropertiesBySubCat = $propperty_assign->LoadUnAssignPropertiesBySubCat($request);
        echo json_encode($objUnassignPropertiesBySubCat);
    }

    public function CheckProperty(Request $request){

        $property_name = $request->property_name;
        $recCount = itemproperty::where('property_name','=',$property_name)->count();

        echo json_encode(array('recordscount' => $recCount));


    }



    public function load_un_assign_list(Request $request){
      $subCatCode = $request->subCatCode;

      $subCat = DB::table('item_property')
      ->select('item_property.property_id','item_property.property_name')
      ->whereNotIn('item_property.property_id',function($q) use ($subCatCode){
         $q->select('property_id')->from('item_property_assign')->where('subcategory_id',$subCatCode);})
         ->get();

      /*$subCat = itemproperty::select('item_property_assign.*','item_property.*')
         ->join('item_property_assign','item_property_assign.property_id','=','item_property.property_id')
         ->where('item_property_assign.subcategory_id' , '=', $subCatCode )
         ->get();*/
         //$check_company = Company::where([['status', '=', '1'],['group_id','=',$id]])->first();

         return response([ 'count' => sizeof($subCat), 'subCat'=> $subCat ]);


    }

    public function load_un_assign_list2(Request $request){
      $subCatCode2 = $request->subCatCode2;
      $subCat2 = itemproperty::select('item_property_assign.*','item_property.*')
         ->join('item_property_assign','item_property_assign.property_id','=','item_property.property_id')
         ->where('item_property_assign.subcategory_id' , '=', $subCatCode2 )
         ->get();

         return response([ 'count2' => sizeof($subCat2), 'subCat2'=> $subCat2 ]);


    }

    public function save_assign(Request $request){

      $propid = $request->propid;
      $formData = $request->formData;

    //  print_r($formData);

      $propperty_assign = new assign_property();
      $propperty_assign->property_id = $propid;
      $propperty_assign->subcategory_id = $formData['sub_category_code'];
      $propperty_assign->status = 1;
      //$propperty_assign->sequence_no = $request->sequence_no;

      $propperty_assign->saveOrFail();




    }



}
