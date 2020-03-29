<?php

namespace App\Http\Controllers\Merchandising\Item;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use App\Models\Merchandising\Item\ItemProperty;
use App\Models\Merchandising\Item\Item;
use App\Models\Merchandising\Item\PropertyValueAssign;
use App\Models\Merchandising\Item\Category;
use App\Models\Merchandising\Item\SubCategory;
use App\Models\Merchandising\Item\AssignProperty;

class ItemPropertyController extends Controller
{

    public function index(Request $request)
    {
        $type = $request->type;

        if($type == 'assigned_properties'){
          $sub_category = $request->sub_category;
          return response([
            'data' => $this->load_assign_properties($sub_category)
          ]);
        }
        else if($type == 'property_values'){
          $property_id = $request->property_id;
          return response([
            'data' => $this->load_property_values($property_id)
          ]);
        }
        /*$keyword = $request->get('search');
        $perPage = 25;

        if (!empty($keyword)) {
            $itemproperty = itemproperty::where('property_id', 'LIKE', "%$keyword%")
                ->latest()->paginate($perPage);
        } else {
            $itemproperty = itemproperty::latest()->paginate($perPage);
        }

        $data = array(
          'categories' => Category::all()
        );*/

        //return view('itemproperty.itemproperty', compact('itemproperty',$data));
      //  return view('itemproperty.itemproperty',$data);
    }


    public function create()
    {
      //  return view('itemproperty.itemproperty.create');
    }


    public function store(Request $request)
    {

        $requestData = $request->all();

        ItemProperty::create($requestData);

        return redirect('itemproperty')->with('flash_message', 'itemproperty added!');
    }


    public function show($id)
    {
        $itemproperty = ItemProperty::findOrFail($id);

        return view('itemproperty.itemproperty.show', compact('itemproperty'));
    }


    public function edit($id)
    {
        $itemproperty = ItemProperty::findOrFail($id);

        return view('itemproperty.itemproperty.edit', compact('itemproperty'));
    }


    public function update(Request $request, $id)
    {

        $requestData = $request->all();

        $itemproperty = ItemProperty::findOrFail($id);
        $itemproperty->update($requestData);

        return redirect('itemproperty')->with('flash_message', 'itemproperty updated!');
    }


    public function destroy($id)
    {
        ItemProperty::destroy($id);

        return redirect('itemproperty')->with('flash_message', 'itemproperty deleted!');
    }

    public function SaveItemProperty(Request $request){

        $item_properties = new ItemProperty();

        $item_properties->property_name = $request->property_name;
        $item_properties->status = 1;
        $item_properties->saveOrFail();

         echo json_encode(array('status' => 'success'));
    }

    public function LoadProperties(){

        $item_property = ItemProperty::where('status','=','1')->pluck('property_id','property_name');

        echo json_encode($item_property);
    }

    public function RemoveAssign(Request $request){

        $propperty_assign = new AssignProperty();

        echo json_encode("Code : ".$request->sub_code);

        $propperty_assign::where('subcategory_id',$request->sub_code)->delete();


    }

    public function SavePropertyAssign(Request $request){

        $propperty_assign = new AssignProperty();


        $obj = AssignProperty::where('property_id',$request->property_id)->where('subcategory_id',$request->subcategory_code);

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


    private function load_assign_properties($sub_category){
        $propperty_assign = new ItemProperty();
        $arr = $propperty_assign->load_assign_properties($sub_category);
        for($x = 0 ; $x < sizeof($arr) ; $x++) {
            $arr[$x]->property_values = $this->load_property_values($arr[$x]->property_id);
            $arr[$x]->data1 = 0;
        }
        return $arr;
    }

    public function LoadUnAssignPropertiesBySubCat(Request $request){
        $propperty_assign = new ItemProperty();

        $subcatcode = $request->subcategory_code;
        $objUnassignPropertiesBySubCat = $propperty_assign->LoadUnAssignPropertiesBySubCat($request);
        echo json_encode($objUnassignPropertiesBySubCat);
    }

    public function CheckProperty(Request $request){

        $property_name = $request->property_name;
        $recCount = ItemProperty::where('property_name','=',$property_name)->count();

        echo json_encode(array('recordscount' => $recCount));


    }



    public function load_un_assign_list(Request $request){
      $subCatCode = $request->subCatCode;

      $subCat = DB::table('item_property')
      ->select('item_property.property_id','item_property.property_name')
      ->where('item_property.status' , '<>', 0 )
      ->whereNotIn('item_property.property_id',function($q) use ($subCatCode){
         $q->select('property_id')
         ->from('item_property_assign')
         ->where('subcategory_id',$subCatCode)
         ->where('status', '<>', 0 )
         ;})
         ->orderBy('property_name', 'ASC')
         ->get();

         return response([ 'count' => sizeof($subCat), 'subCat'=> $subCat ]);


    }

    public function load_un_assign_list2(Request $request){
      $subCatCode2 = $request->subCatCode2;
      $subCat2 = ItemProperty::select('item_property_assign.*','item_property.*')
         ->join('item_property_assign','item_property_assign.property_id','=','item_property.property_id')
         ->where('item_property_assign.subcategory_id' , '=', $subCatCode2 )
         ->where('item_property_assign.status' , '<>', 0 )
         ->orderByRaw('sequence_no ASC')
         ->get();

         return response([ 'count2' => sizeof($subCat2), 'subCat2'=> $subCat2 ]);

    }


    public function save_assign(Request $request){

      $propid = $request->propid;
      $formData = $request->formData;

      $check_ = Item::select(DB::raw('count(*) as sub_count'))
                   ->where('subcategory_id', '=', $formData['sub_category_code'])
                   ->where('status', '<>', 0)
                   ->get();
      if($check_[0]['sub_count'] >=1 ){

    //  return response([ 'data' => ['status' => 'error','message' => 'Sub Category already exists !']]);
      return ['status' => 'error','message' => 'Sub Category already exists !'];

      }else{

        $propperty_assign = new AssignProperty();
        $propperty_assign->property_id = $propid;
        $propperty_assign->subcategory_id = $formData['sub_category_code'];
        $propperty_assign->status = 1;
        $propperty_assign->sequence_no = $this->get_next_line($formData['sub_category_code']);
        $propperty_assign->saveOrFail();

        return ['status' => 'success'];
      }

    }



    private function get_next_line($subid)
      {
        $max_no = AssignProperty::where('subcategory_id','=',$subid)->max('sequence_no');
  	  if($max_no == NULL){ $max_no= 0;}
        return ($max_no + 1);
      }

    private function load_property_values($property_id){
        $list = PropertyValueAssign::where('property_id', '=', $property_id)->get();
        return $list;
    }




    public function final_save_assign(Request $request) {

      $List = $request->Assign;
      //print_r($List);

      for($x = 0 ; $x < sizeof($List) ; $x++) {


        DB::table('item_property_assign')
            ->where('property_assign_id', $List[$x]['property_assign_id'])
            ->where('property_id', $List[$x]['property_id'])
            ->update(['sequence_no' => ($x + 1)]);


      }

      return response([ 'data' => [
        'message' => 'Item Property assigned successfully',
        'proid' => $List[0]['subcategory_id']
      ]]);



    }

    //validate anything based on requirements
    public function validate_data(Request $request){
      $for = $request->for;
      if($for == 'duplicate')
      {
        return response($this->validate_duplicate_code($request->source_id , $request->property_name));
      }
    }

    //check Source code already exists
    private function validate_duplicate_code($id , $code)
    {
      $source = ItemProperty::where([['status', '=', '1'],['property_name','=',$code]])->first();
      //  echo $source;
      if($source == null){
        return ['status' => 'success'];
      }
      else if($source->property_id == $id){
        return ['status' => 'success'];
      }
      else {
        return ['status' => 'error','message' => 'property already exists'];
      }
    }



    public function save_pro_name(Request $request){
        $pro_name = $request->pro_name;
        $formData = $request->formData;

        $item_properties = new ItemProperty();
        $item_properties->property_name = strtoupper($pro_name['property_name']);
        $item_properties->status = 1;
        $item_properties->saveOrFail();

        return response([ 'data' => [
          'message' => 'Property saved successfully',
          'proid' => $formData['sub_category_code']
          ]
        ]);
    }

    public function remove_assign(Request $request){



        $List = $request->Assign;
        $proid = $request->proid;
        $formData = $request->formData;
        $check_ = Item::select(DB::raw('count(*) as sub_count'))
                     ->where('subcategory_id', '=', $formData['sub_category_code'])
                     ->where('status', '<>', 0)
                     ->get();
        if($check_[0]['sub_count'] >=1 ){

        return response([ 'data' => ['status' => 'error','message' => 'Sub Category already exists !']]);

        }else{

          DB::table('item_property_assign')
            ->where('subcategory_id', $formData['sub_category_code'])
            ->where('property_id', $proid)
            ->update(['status' => 0]);

          return response([ 'data' => [
          'message' => 'Property Deleted successfully',
          'proid' => $formData['sub_category_code']
          ]
        ]);

      }



    }


    public function remove_unassign(Request $request){

        $List = $request->UNAssign;
        $proid = $request->proid;
        $formData = $request->formData;

        $check = AssignProperty::where([['status', '=', '1'],['property_id','=',$proid]])->first();
        if($check != null)
        {
          return response([
            'data'=>[
              'status'=>'0',
            ]
          ]);
        }else{

        DB::table('item_property')
            ->where('property_id', $proid)
            ->update(['status' => 0]);

        return response([ 'data' => [
          'message' => 'Property Deleted successfully',
          'proid' => $formData['sub_category_code']
          ]
        ]);
      }
    }


}
