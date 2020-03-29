<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\customesize;
use Illuminate\Http\Request;


use App\Division;

class customesizesController extends Controller
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
        //$division[] = array('KIDS','T16','T14');
        if (!empty($keyword)) {
            $customesizes = customesize::where('size_id', 'LIKE', "%$keyword%")
                ->orWhere('customer_id', 'LIKE', "%$keyword%")
                ->orWhere('division_id', 'LIKE', "%$keyword%")
                ->orWhere('size_name', 'LIKE', "%$keyword%")
                ->latest()->paginate($perPage);
        } else {
            $customesizes = customesize::latest()->paginate($perPage);
        }
        
        //$division = Division::where('customer_code','=','1')->pluck('division_description', 'division_id');

        return view('customesizes.customesizes.index', compact('customesizes'));
        //return view('customesizes.customesizes.index', compact('customesizes','division'));
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('customesizes.customesizes.create');
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
        
        customesize::create($requestData);

        return redirect('customesizes')->with('flash_message', 'customesize added!');
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
        $customesize = customesize::findOrFail($id);

        return view('customesizes.customesizes.show', compact('customesize'));
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
        $customesize = customesize::findOrFail($id);

        return view('customesizes.customesizes.edit', compact('customesize'));
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
        
        $customesize = customesize::findOrFail($id);
        $customesize->update($requestData);

        return redirect('customesizes')->with('flash_message', 'customesize updated!');
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
        customesize::destroy($id);

        return redirect('customesizes')->with('flash_message', 'customesize deleted!');
    }
    
    public function GetDivisionsByCustomer(Request $request){
       
       $customerCode = $request->customerCode;
        
       $division = Division::where('customer_code','=',$customerCode)->pluck('division_id', 'division_description'); 
       
       return json_encode($division);
    }
    
    public function SaveSizes(Request $request){
        
        $custome_sizes = new customesize();
        
        if($request->size_hid>0){
            $custome_sizes = customesize::find($request->size_hid);
            $custome_sizes->size_name = $request->size_name;
            
        }else{
            $custome_sizes->customer_id = $request->customer_code;
            $custome_sizes->division_id = $request->division_code;
            $custome_sizes->size_name = $request->size_name;
        }
        
        //$custome_sizes->save();
        $custome_sizes->saveOrFail();
        
        
        echo json_encode(array('status' => 'success','message'=>$request->size_name));
        
    }
    
    public function LoadCustomeSizes(){
        
        //$sizelist = customesize::all();
        //$sizelist = DB::table('cust_sizes')->join('cust_division','cust_division.division_id','=','cust_sizes.division_id' )->select('cust_sizes.*','cust_division.division_description')->get();
        $sizelist = customesize::LoadCustomeSizeList();
        echo json_encode($sizelist);
    }
    
    public function EditCustomeSizes(Request $request){
        
        $customesize_id = $request->size_id;
        $customesizes = customesize::find($customesize_id);
        echo json_encode($customesizes);
    }
    
    public function DeleteCustomeSizes(Request $request){
                
        $customesize_id = $request->size_id;        
        
        $custom_sizes = customesize::where('size_id',$customesize_id)->update(['status'=>'0']);
        echo json_encode(array('delete'));
        
    }
    
    
    
}
