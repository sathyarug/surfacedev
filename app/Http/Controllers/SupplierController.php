<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\OrgSupplier;
use App\OrgLocation;
use App\Currency;
use App\Country;
use App\OrgSupplierCurrencyMap;
use App\OrgShipmentMode;
use App\Models\Org\OriginType;
use App\Models\Finance\Accounting\PaymentMethod;
use App\Models\Finance\Accounting\PaymentTerm;

class SupplierController extends Controller
{
    public function view()
    {
        $locs=OrgLocation::all()->toArray();
        $PaymentMethods=PaymentMethod::all()->toArray();
        $PaymentTerms=PaymentTerm::all()->toArray();
        $CurrencyListAll=Currency::all()->toArray();

        $loction=array(''=>'');
        foreach ($locs AS $loc ){
            $loction[$loc['loc_id']]=$loc['loc_name'];
        }
        $method=array(''=>'');
        foreach ($PaymentMethods AS $PaymentMethod ){
            $method[$PaymentMethod['payment_method_id']]=$PaymentMethod['payment_method_code'];
        }
        $terms=array(''=>'');
        foreach ($PaymentTerms AS $PaymentTerm ){
            $terms[$PaymentTerm['payment_term_id']]=$PaymentTerm['payment_code'];
        }
        $currency=array(''=>'');
        foreach ($CurrencyListAll AS $CurrencyList ){
            $currency[$CurrencyList['currency_id']]=$CurrencyList['currency_code'];
        }


        return view('supplier/supplier', ['loc' =>$loction,'method'=>$method,'terms'=>$terms,'currency'=>$currency]);
    }

    public function getList() {
        return datatables()->of(OrgSupplier::all()->sortByDesc("supplier_id")->sortByDesc("status"))->toJson();
    }

    public function saveSupplier(Request $request) {

        $OrgSupplier = new OrgSupplier();
//        $OrgSupplierCurrencyMap = new OrgSupplierCurrencyMap();

        if ($OrgSupplier->validate($request->all()))
        {
            if($request->supplier_hid > 0){
                $OrgSupplier = OrgSupplier::find($request->supplier_hid);
            }
            $OrgSupplier->fill($request->all());
            $OrgSupplier->status = 1;
            $OrgSupplier->created_by = 1;
            $result = $OrgSupplier->saveOrFail();
            $id=$OrgSupplier->supplier_id;
            //$OrgSupplier->supplier_id
            $currencyAll=$request->currency;

            OrgSupplierCurrencyMap::where('supplier_id', '=', $id)->update(['status' => 0]);

//            print_r($id);exit;

            $updateCurrency=array();
            foreach ($currencyAll AS $currency){
                $OrgSupplierCurrencyMap = new OrgSupplierCurrencyMap();
                $OrgSupplierCurrencyMapCheck = OrgSupplierCurrencyMap::where('supplier_id', $id)
                    ->where('currency_id', $currency)->get()->toArray();

                if(count($OrgSupplierCurrencyMapCheck) != 0){
                    $OrgSupplierCurrencyMap = OrgSupplierCurrencyMap::find($OrgSupplierCurrencyMapCheck[0]['supplier_currency_map_id']);
                    $OrgSupplierCurrencyMap->updated_date = 1;
                    $OrgSupplierCurrencyMap->updated_by = Auth::id();
                }else{
                    $OrgSupplierCurrencyMap->created_date = 1;
                    $OrgSupplierCurrencyMap->created_by = Auth::id();
                }
                $OrgSupplierCurrencyMap->supplier_id =$id;
                $OrgSupplierCurrencyMap->currency_id = $currency;
                $OrgSupplierCurrencyMap->status = 1;
                $OrgSupplierCurrencyMap->saveOrFail();

                // dump($OrgSupplierCurrencyMap->saveOrFail());
            }
            echo json_encode(array('status' => 'success' , 'message' => 'Source details saved successfully.') );
        }
        else
        {
            // failure, get errors
            $errors = $OrgSupplier->errors();
            echo json_encode(array('status' => 'error' , 'message' => $errors));
        }


    }

    public function loadEditSupplier(Request $request) {
        $Supplier_id = $request->id;
        $Supplier = OrgSupplier::find($Supplier_id);
        echo json_encode($Supplier);
    }

    public function deleteSupplier(Request $request) {
        $Supplier_id = $request->id;
        $source = OrgSupplier::where('supplier_id', $Supplier_id)->update(['status' => 0]);
        echo json_encode(array('delete'));
    }

    public function loadAddEditSupplier(Request $request) {

        $Supplier_id = $request->id;
        $Supplier = OrgSupplier::find($Supplier_id);

        $locs=Country::all()->toArray();
//        $locs=OrgLocation::all()->toArray();
        $PaymentMethods=PaymentMethod::all()->toArray();
        $PaymentTerms=PaymentTerm::all()->toArray();
        $CurrencyListAll=Currency::all()->toArray();
        $originListAll=OriginType::all()->toArray();
        $OrgShipmentModeListAll=OrgShipmentMode::all()->toArray();

        $OrgSupplierCurrencyMap = OrgSupplierCurrencyMap::where('supplier_id', $Supplier_id)->
        where('status', 1)->get()->pluck('currency_id', 'currency_id');


        $loction=array(''=>'');
        foreach ($locs AS $loc ){
            $loction[$loc['country_id']]=$loc['country_code'];
        }
        $method=array(''=>'');
        foreach ($PaymentMethods AS $PaymentMethod ){
            $method[$PaymentMethod['payment_method_id']]=$PaymentMethod['payment_method_code'];
        }
        $terms=array(''=>'');
        foreach ($PaymentTerms AS $PaymentTerm ){
            $terms[$PaymentTerm['payment_term_id']]=$PaymentTerm['payment_code'];
        }
        $currency=array(''=>'');
        foreach ($CurrencyListAll AS $CurrencyList ){
            $currency[$CurrencyList['currency_id']]=$CurrencyList['currency_code'];
        }
        $origin=array(''=>'');
        foreach ($originListAll AS $originList ){
            $origin[$originList['origin_type_id']]=$originList['origin_type'];
        }

        $ShipmentMode=array(''=>'');
        foreach ($OrgShipmentModeListAll AS $OrgShipmentModeList ){
            $ShipmentMode[$OrgShipmentModeList['shipment_mode_id']]=$OrgShipmentModeList['shipment_mode'];
        }
        return view('supplier.frmsupplier',['loc' =>$loction,'method'=>$method,'terms'=>$terms,'currency'=>$currency,'Supplier'=>$Supplier,'origin'=>$origin,'ShipmentMode'=>$ShipmentMode,'CurrencyMap'=>$OrgSupplierCurrencyMap]);
    }
}
