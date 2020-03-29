<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

/*
routing responses and codes
...................................................
HTTP_OK = 200;
HTTP_CREATED = 201;
HTTP_NO_CONTENT = 204;
HTTP_BAD_REQUEST = 400;
HTTP_UNAUTHORIZED = 401;
HTTP_NOT_FOUND = 404;
HTTP_METHOD_NOT_ALLOWED = 405;
HTTP_CONFLICT = 409;
HTTP_INTERNAL_SERVER_ERROR = 500;
*/


Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {

    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');
    Route::get('validate_mail', 'AuthController@validate_mail');
    Route::post('send_confirmation', 'AuthController@send_confirmation');
    Route::post('save_new_password', 'AuthController@save_new_password');
    Route::post('confirm_link', 'AuthController@confirm_link');

});

  Route::get('test-email' , 'TestController@send_mail');

//org routing.................................

/*
GET|HEAD  | api/countries  | countries.index    | App\Http\Controllers\Org\CountryController@index
POST      | api/countries | countries.store    | App\Http\Controllers\Org\CountryController@store
PUT|PATCH | api/countries/{country}  | countries.update   | App\Http\Controllers\Org\CountryController@update
GET|HEAD  | api/countries/{country}  | countries.show     | App\Http\Controllers\Org\CountryController@show
DELETE    | api/countries/{country}  | countries.destroy  | App\Http\Controllers\Org\CountryController@destroy */
Route::prefix('org/')->group(function(){

    Route::get('countries/validate' , 'Org\CountryController@validate_data');
    Route::apiResource('countries','Org\CountryController');

    Route::get('sections/validate' , 'Org\SectionController@validate_data');
    Route::apiResource('sections','Org\SectionController');

    Route::get('departments/validate' , 'Org\DepartmentController@validate_data');
    Route::apiResource('departments','Org\DepartmentController');

    Route::get('sources/validate' , 'Org\Location\SourceController@validate_data');
    Route::apiResource('sources','Org\Location\SourceController');

    Route::get('clusters/validate' , 'Org\Location\ClusterController@validate_data');
    Route::apiResource('clusters','Org\Location\ClusterController');

    Route::get('companies/validate' , 'Org\Location\CompanyController@validate_data');
    Route::apiResource('companies','Org\Location\CompanyController');

    Route::get('locations/validate' , 'Org\Location\LocationController@validate_data');
    Route::apiResource('locations','Org\Location\LocationController');

    Route::apiResource('location-types','Org\LocationTypeController');
    Route::apiResource('property-types','Org\PropertyTypeController');

    Route::get('customers/validate' , 'Org\CustomerController@validate_data');
    Route::get('customers/divisions' , 'Org\CustomerController@customer_divisions');
    Route::put('customers/divisions' , 'Org\CustomerController@save_customer_divisions');
    Route::apiResource('customers','Org\CustomerController');

    Route::get('suppliers/validate' , 'Org\SupplierController@validate_data');
    Route::apiResource('suppliers','Org\SupplierController');
    Route::post('suppliers/load_currency','Org\SupplierController@load_currency');

    Route::get('supplierslist/loadsuppliers' , 'Org\SupplierController@loadSuppliers');
    Route::apiResource('supplierslist','Org\SupplierController');

    Route::get('uom/validate' , 'Org\UomController@validate_data');
    Route::apiResource('uom','Org\UomController');

    Route::get('cancellation-categories/validate' , 'Org\Cancellation\CancellationCategoryController@validate_data');
    Route::apiResource('cancellation-categories','Org\Cancellation\CancellationCategoryController');

    Route::get('cancellation-reasons/validate' , 'Org\Cancellation\CancellationReasonController@validate_data');
    Route::apiResource('cancellation-reasons','Org\Cancellation\CancellationReasonController');

    Route::get('divisions/validate' , 'Org\DivisionController@validate_data');
    Route::apiResource('divisions','Org\DivisionController');

    Route::get('seasons/validate' , 'Org\SeasonController@validate_data');
    Route::apiResource('seasons','Org\SeasonController');

    Route::get('origin-types/validate' , 'Org\OriginTypeController@validate_data');
    Route::apiResource('origin-types','Org\OriginTypeController');

    Route::get('sizes/validate' , 'Org\SizeController@validate_data');
    Route::apiResource('sizes','Org\SizeController');

    Route::get('colors/validate' , 'Org\ColorController@validate_data');
    Route::apiResource('colors','Org\ColorController');

    Route::get('stores/validate' , 'Store\StoreController@validate_data');
    Route::apiResource('stores','Store\StoreController');

    Route::get('product-specification/validate' , 'Org\ProductSpecificationController@validate_data');
    Route::apiResource('product-specifications','Org\ProductSpecificationController');

    Route::get('silhouette-classification/validate' , 'Org\SilhouetteClassificationController@validate_data');
    Route::apiResource('silhouette-classification','Org\SilhouetteClassificationController');

    Route::get('silhouettes/validate' , 'Org\SilhouetteController@validate_data');
    Route::apiResource('silhouettes','Org\SilhouetteController');

    Route::get('customerSizeGrids/validate' , 'Org\CustomerSizeGridController@validate_data');
    Route::apiResource('customerSizeGrids','Org\CustomerSizeGridController');

    Route::get('features/validate' , 'Org\FeatureController@validate_data');
    Route::apiResource('features','Org\FeatureController');
    Route::post('features/load_pro_list', 'Org\FeatureController@load_pro_list');

    Route::get('garmentoptions/validate' , 'Org\GarmentOptionsController@validate_data');
    Route::apiResource('garmentoptions','Org\GarmentOptionsController');

    Route::get('requestType/validate' , 'Org\RequestTypeController@validate_data');
    Route::apiResource('requestType','Org\RequestTypeController');
    Route::get('customerSizeGrids/validate' , 'Org\CustomerSizeGridController@validate_data');
    Route::apiResource('customerSizeGrids','Org\CustomerSizeGridController');

    Route::apiResource('ship-modes','Org\ShipModeController');
    Route::get('designations/validate' , 'Org\DesignationController@validate_data');
    Route::apiResource('designations','Org\DesignationController');

    Route::get('PoType/validate' , 'Org\PoTypeController@validate_data');
    Route::apiResource('PoType','Org\PoTypeController');

    Route::get('silhouette-classification/validate' , 'Org\SilhouetteClassificationController@validate_data');
    Route::apiResource('silhouette-classification','Org\SilhouetteClassificationController');

    //Route::get('features/validate' , 'Org\FeatureController@validate_data');
    //Route::apiResource('features','Org\FeatureController');

    Route::get('silhouettes/validate' , 'Org\SilhouetteController@validate_data');
    Route::apiResource('silhouettes','Org\SilhouetteController');

    Route::get('garmentoptions/validate' , 'Org\GarmentOptionsController@validate_data');
    Route::apiResource('garmentoptions','Org\GarmentOptionsController');

    Route::get('customerSizeGrids/validate' , 'Org\CustomerSizeGridController@validate_data');
    Route::apiResource('customerSizeGrids','Org\CustomerSizeGridController');

  Route::get('requestType/validate' , 'Org\RequestTypeController@validate_data');
  Route::apiResource('requestType','Org\RequestTypeController');
  Route::get('customerSizeGrids/validate' , 'Org\CustomerSizeGridController@validate_data');
  Route::apiResource('customerSizeGrids','Org\CustomerSizeGridController');

  Route::apiResource('ship-modes','Org\ShipModeController');
  Route::get('designations/validate' , 'Org\DesignationController@validate_data');
  Route::apiResource('designations','Org\DesignationController');

  Route::get('PoType/validate' , 'Org\PoTypeController@validate_data');
  Route::apiResource('PoType','Org\PoTypeController');

  Route::get('silhouette-classification/validate' , 'Org\SilhouetteClassificationController@validate_data');
  Route::apiResource('silhouette-classification','Org\SilhouetteClassificationController');

  //Route::get('features/validate' , 'Org\FeatureController@validate_data');
  //Route::apiResource('features','Org\FeatureController');

  Route::apiResource('ship-modes','Org\ShipModeController');

  Route::get('sizes-chart/validate' , 'Org\SizeChartController@validate_data');
  Route::apiResource('sizes-chart','Org\SizeChartController');

  Route::apiResource('conv-factor','Org\ConversionFactorController');

  Route::apiResource('pack-types','Org\PackTypeController');

});



//});

Route::prefix('stores/')->group(function(){
    Route::apiResource('generalpr','stores\GeneralPRController');
    Route::apiResource('generalpr_details','stores\GeneralPRDetailController');
    //Route::get('get_genpr','stores\GeneralPRController');


    Route::apiResource('get_genpr','stores\GeneralPRController');
});

Route::prefix('ie/')->group(function(){

    Route::get('smvupdates/validate' , 'IE\SMVUpdateController@validate_data');
    Route::get('smvupdates/divisions' , 'IE\SMVUpdateController@customer_divisions');
    Route::put('smvupdates/updates' , 'IE\SMVUpdateController@update');
    Route::apiResource('smvupdates','IE\SMVUpdateController');

    Route::apiResource('smvupdatehistories','IE\SMVUpdateHistoryController');
    Route::put('smvupdatehistories/updates' , 'IE\SMVUpdateHistoryController@update');


    Route::get('servicetypes/validate' , 'IE\ServiceTypeController@validate_data');
    Route::apiResource('servicetypes','IE\ServiceTypeController');

  Route::get('servicetypes/validate' , 'IE\ServiceTypeController@validate_data');
  Route::apiResource('servicetypes','IE\ServiceTypeController');
  Route::get('garment_operations/validate' , 'IE\GarmentOperationMasterController@validate_data');
   Route::apiResource('garment_operations','IE\GarmentOperationMasterController');
   Route::apiResource('styles','Merchandising\StyleCreationController');
   Route::apiResource('bomStages','Merchandising\BOMStageController');
   Route::apiResource('componentSMVDetails','IE\ComponentSMVController');
   Route::post('componentSMVDetails/saveDataset','IE\ComponentSMVController@storeDataset');
   Route::post('componentSMVDetails/checkSMVRange' , 'IE\ComponentSMVController@check_smv_range');
   Route::post('componentSMVDetails/checkCopyStatus' , 'IE\ComponentSMVController@check_copy_status');
    //Route::apiResource('garment_operations','IE\GarmentOperationMasterController');

});

Route::prefix('items/')->group(function(){
    Route::get('itemlists/loadItemList' , 'itemCreationController@GetItemList');
    Route::apiResource('itemlists','itemCreationController');

    Route::get('itemlist/loadItemsbycat' , 'itemCreationController@GetItemListBySubCategory');
    Route::apiResource('itemlist','itemCreationController');

    Route::get('getitem/getItemByCode' , 'itemCreationController@GetItemDetailsByCode');
    Route::apiResource('getitem','itemCreationController');
    //item cretaion duplicate validataion
    Route::get('itemCreation/validate' , 'itemCreationController@validate_data');
    Route::get('garment_operations/validate' , 'IE\GarmentOperationMasterController@validate_data');
    Route::apiResource('itemCreation','itemCreationController');


});




Route::prefix('finance/')->group(function(){

    Route::get('goods-types/validate' , 'Finance\GoodsTypeController@validate_data');
    Route::apiResource('goods-types','Finance\GoodsTypeController');

    Route::get('ship-terms/validate' , 'Finance\ShipmentTermController@validate_data');
    Route::apiResource('ship-terms','Finance\ShipmentTermController');

    Route::get('accounting/payment-methods/validate' , 'Finance\Accounting\PaymentMethodController@validate_data');
    Route::apiResource('accounting/payment-methods','Finance\Accounting\PaymentMethodController');

    Route::get('accounting/payment-terms/validate' , 'Finance\Accounting\PaymentTermController@validate_data');
    Route::apiResource('accounting/payment-terms','Finance\Accounting\PaymentTermController');

    Route::get('accounting/cost-centers/validate' , 'Finance\Accounting\CostCenterController@validate_data');
    Route::apiResource('accounting/cost-centers','Finance\Accounting\CostCenterController');

    Route::get('currencies/validate' , 'Finance\CurrencyController@validate_data');
    Route::apiResource('currencies','Finance\CurrencyController');

    Route::get('exchange-rates/validate' , 'Finance\ExchangeRateController@validate_data');
    Route::apiResource('exchange-rates','Finance\ExchangeRateController');

    Route::get('transaction/validate' , 'Finance\TransactionController@validate_data');
    Route::apiResource('transaction','Finance\TransactionController');
    //sub category duplication validate
    Route::get('subcategory/validate' , 'Finance\Item\ItemSubCategoryController@check_sub_category_code');

    Route::apiResource('finCost','Finance\Cost\FinanceCostController');

    Route::apiResource('finCostHis','Finance\Cost\FinanceCostHistoryController');
    Route::put('finCostHis/updates' , 'Finance\Cost\FinanceCostHistoryController@update');

});


Route::prefix('stores/')->group(function(){

  Route::apiResource('po-load','stores\RollPlanController');
  Route::apiResource('roll','stores\RollPlanController');
  /********edited*/
  Route::get('supplier-tolarance/validate' , 'Stores\SupplierTolaranceController@validate_data');
  Route::apiResource('supplier-tolarance','Stores\SupplierTolaranceController');
  Route::apiResource('fabricInspection','Store\FabricInspectionController');
  Route::get('transfer-location/validate' , 'Stores\TransferLocationController@validate_data');
  Route::post('transfer-location-store','Stores\TransferLocationController@storedetails');
  Route::post('transfer-location/approval/send','Stores\TransferLocationController@send_to_approval');
  Route::apiResource('transfer-location','Stores\TransferLocationController');
  Route::apiResource('grn', 'Store\GrnController');
  Route::post('save-grn-bin', 'Store\GrnController@saveGrnBins');
  Route::get('load-grn-lines', 'Store\GrnController@loadAddedGrnLInes');
  Route::get('loadPoBinList','Store\StoreBinController@getBinListByLoc');
  Route::get('loadAddedBins','Store\GrnController@getAddedBins');
  Route::get('load-substores','Store\SubStoreController@getSubStoreList');
  Route::post('grn/filterData','Store\GrnController@filterData');
  Route::get('grns/validate' , 'Store\GrnController@validate_data');
  Route::get('grns/deleteGrnLine' , 'Store\GrnController@deleteLine');

  //sub store bin validate
  Route::get('subStoreBin/validate' , 'Store\StoreBinController@validate_data');
  Route::apiResource('substore','Store\StoreBinController');
  Route::get('substore-bin-list','Store\SubStoreController@getSubStoreBinList');
  Route::get('load-bin-qty','Store\BinTransferController@loadBinQty');
  Route::get('load-added-bin-qty','Store\BinTransferController@loadAddedBinQty');
  Route::post('add-bin-qty','Store\BinTransferController@addBinTrnsfer');
  Route::apiResource('save-bin-transfer', 'Store\BinTransferController');

  Route::post('load-stock-for-mrn','Store\StockController@searchStock');
  Route::apiResource('po-load','stores\RollPlanController');
  Route::apiResource('roll','stores\RollPlanController');
    /********edited*/
  Route::get('supplier-tolarance/validate' , 'Stores\SupplierTolaranceController@validate_data');
  Route::apiResource('supplier-tolarance','Stores\SupplierTolaranceController');
  //Route::apiResource('fabricInspection','stores\FabricInspectionController');
  Route::get('transfer-location/validate' , 'Stores\TransferLocationController@validate_data');
  Route::post('transfer-location-store','Stores\TransferLocationController@storedetails');
  Route::apiResource('transfer-location','Stores\TransferLocationController');
  Route::apiResource('grn', 'Store\GrnController');
  Route::post('save-grn-bin', 'Store\GrnController@saveGrnBins');
  Route::get('load-grn-lines', 'Store\GrnController@loadAddedGrnLInes');
  Route::get('loadPoBinList','Store\StoreBinController@getBinListByLoc');
  Route::get('loadAddedBins','Store\GrnController@getAddedBins');
  Route::get('load-substores','Store\SubStoreController@getSubStoreList');
  Route::get('substore-bin-list','Store\SubStoreController@getSubStoreBinList');
  Route::get('load-bin-qty','Store\BinTransferController@loadBinQty');
  Route::get('load-added-bin-qty','Store\BinTransferController@loadAddedBinQty');
  Route::post('add-bin-qty','Store\BinTransferController@addBinTrnsfer');
  Route::apiResource('save-bin-transfer', 'Store\BinTransferController');

    //Route::get('transfer-location/validate' , 'Stores\TransferLocationController@validate_data');
    //Route::post('material-transfer','Stores\MaterialTransferController@datatable_search');
  //Route::get('material-transfer','Stores\MaterialTransferController@getStores');
  Route::apiResource('material-transfer','Stores\MaterialTransferController');
  Route::post('material-transfer-store','Stores\MaterialTransferController@storedetails');

    //Route::apiResource('substore','Store\SubStoreController');
  Route::get('stock-bal-for-return-to-sup','Store\StockController@getStockForReturnToSup');
  Route::get('isreadyForRollPlan','Store\GrnController@isreadyForRollPlan');
  Route::get('isreadyForTrimPackingDetails','Store\GrnController@isreadyForTrimPackingDetails');
  Route::get('searchRollPlanDetails','Store\FabricInspectionController@search_rollPlan_details');
  //Route::post('fabricInspection/autoBatchNoFilter','Store\FabricInspectionController@autocomplete_search_bacth_filter');
});
Route::prefix('d2d/')->group(function(){

    Route::post('load_d2d_user','D2d\D2DController@load_d2d_user');

});

Route::prefix('fastreact/')->group(function(){

    Route::apiResource('get-data','Fastreact\FastReactController');
    Route::post('load_fr_Details','Fastreact\FastReactController@load_fr_Details');
    Route::get('export_csv' , 'Fastreact\FastReactController@export_csv');
    Route::get('export_csv_orders' , 'Fastreact\FastReactController@export_csv_orders');

});



Route::prefix('merchandising/')->group(function(){

//  Route::get('g/validate' , 'Finance\GoodsTypeController@validate_data');
    Route::apiResource('customer-orders','Merchandising\CustomerOrderController');
    Route::post('load_header_season' , 'Merchandising\CustomerOrderController@load_header_season');
    Route::post('load_header_stage' , 'Merchandising\CustomerOrderController@load_header_stage');
    Route::post('load_header_buy_name' , 'Merchandising\CustomerOrderController@load_header_buy_name');

    Route::post('cod/copy_line','Merchandising\CustomerOrderDetailsController@copy_line');
    Route::post('load_colour_type' , 'Merchandising\CustomerOrderDetailsController@load_colour_type');
    Route::post('cod/delete_line','Merchandising\CustomerOrderDetailsController@delete_line');
    Route::post('released_SO','Merchandising\CustomerOrderDetailsController@released_SO');
    Route::post('released_SO_All','Merchandising\CustomerOrderDetailsController@released_SO_All');
    Route::post('change_style_colour' , 'Merchandising\CustomerOrderDetailsController@change_style_colour');
    Route::post('load_fng' , 'Merchandising\CustomerOrderDetailsController@load_fng');
    Route::post('load_fng_colour' , 'Merchandising\CustomerOrderDetailsController@load_fng_colour');
    Route::post('load_fng_country' , 'Merchandising\CustomerOrderDetailsController@load_fng_country');
    Route::post('full_deactivate','Merchandising\CustomerOrderController@full_deactivate');

    Route::post('customer-order-details/split-delivery','Merchandising\CustomerOrderDetailsController@split_delivery');
    Route::post('customer-order-details/merge','Merchandising\CustomerOrderDetailsController@merge');
    Route::get('customer-order-details/revisions','Merchandising\CustomerOrderDetailsController@revisions');
    Route::get('customer-order-details/origins','Merchandising\CustomerOrderDetailsController@origins');
    Route::apiResource('customer-order-details','Merchandising\CustomerOrderDetailsController');

    Route::apiResource('customer-order-sizes','Merchandising\CustomerOrderSizeController');
    Route::apiResource('customer-order-types','Merchandising\CustomerOrderTypeController');
    Route::apiResource('get-style','Merchandising\StyleCreationController');
    Route::apiResource('tna-master','Merchandising\TnaMasterController');
    Route::get('color-options/validate' , 'Merchandising\ColorOptionController@validate_data');
    Route::apiResource('color-options','Merchandising\ColorOptionController');

    Route::get('position/validate' , 'Merchandising\PositionController@validate_data');
    Route::apiResource('position','Merchandising\PositionController');

    Route::get('style/validate' , 'Merchandising\StyleCreationController@validate_data');
    Route::apiResource('style','Merchandising\StyleCreationController');
    Route::post('pro_listload', 'Merchandising\StyleCreationController@pro_listload');


    Route::get('rounds/validate' , 'Merchandising\RoundController@validate_data');
    Route::apiResource('rounds','Merchandising\RoundController');

    Route::get('bomstages/validate' , 'Merchandising\BOMStageController@validate_data');
    Route::apiResource('bomstages','Merchandising\BOMStageController');

    Route::get('cut-direction/validate' , 'Merchandising\CutDirectionController@validate_data');
    Route::apiResource('cut-direction','Merchandising\CutDirectionController');

    Route::get('matsize/validate' , 'Merchandising\MaterialSizeController@validate_data');
    Route::get('matsize/subcat', 'Merchandising\MaterialSizeController@get_sub_cat');
    Route::apiResource('matsize','Merchandising\MaterialSizeController');

    Route::get('loadPoLineData','Merchandising\PurchaseOrder@loadPoLineData');
    Route::get('loadPoSCList','Merchandising\PurchaseOrder@getPoSCList');
    Route::get('loadCostingData','Merchandising\PurchaseOrder@getCostingData');
    Route::apiResource('purchase-order-data','Merchandising\PurchaseOrder');

    Route::get('loadCostingDataForCombine','Merchandising\Costing\CostingController@getCostingDataForCombine');
    Route::get('costing/validate' , 'Merchandising\Costing\CostingController@validate_data');
    //Route::post('costing/finish-good/copy','Merchandising\Costing\CostingController@copy_finish_good');
    Route::post('costing/finish-good/delete','Merchandising\Costing\CostingController@delete_finish_good');

    Route::post('costing/copy','Merchandising\Costing\CostingController@copy');
    Route::post('costing/approval/send','Merchandising\Costing\CostingController@send_to_approval');
    Route::get('costing/approve','Merchandising\Costing\CostingController@approve_costing');
    Route::post('costing/update-size-chart', 'Merchandising\Costing\CostingController@update_size_chart');
    Route::post('costing/save-costing-colors', 'Merchandising\Costing\CostingController@save_costing_colors');
    Route::post('costing/remove-costing-color', 'Merchandising\Costing\CostingController@remove_costing_color');
    Route::post('costing/save-costing-countries', 'Merchandising\Costing\CostingController@save_costing_countries');
    Route::post('costing/remove-costing-country', 'Merchandising\Costing\CostingController@remove_costing_country');
    Route::post('costing/generate-bom', 'Merchandising\Costing\CostingController@genarate_bom');
    Route::post('costing/edit-mode', 'Merchandising\Costing\CostingController@edit_mode');
    Route::apiResource('costing','Merchandising\Costing\CostingController');

    Route::apiResource('costing-design-sources','Merchandising\Costing\CostingDesignSourceController');

    //Route::post('costing-finish-good-items-save','Merchandising\Costing\CostingFinishGoodItemController@save_items');
    //Route::post('costing-finish-good-items-copy','Merchandising\Costing\CostingFinishGoodItemController@copy');
    //Route::apiResource('costing-finish-good-items','Merchandising\Costing\CostingFinishGoodItemController');
    Route::post('costing-items-save','Merchandising\Costing\CostingItemController@save_items');
    Route::post('costing-items-copy','Merchandising\Costing\CostingItemController@copy');
    Route::post('costing-items/copy-component-items','Merchandising\Costing\CostingItemController@copy_feature_component_items');
    Route::apiResource('costing-items','Merchandising\Costing\CostingItemController');

    Route::apiResource('costing-so-deliveries','Merchandising\Costing\CostingSalesOrderDeliveryController');



  //  Route::get('bulk/validate' , 'Merchandising\BulkCosting\BulkDetailsController@validate_data');
  //  Route::apiResource('bulk','Merchandising\BulkCosting\BulkDetailsController');


    //Route::get('loadSoList','Merchandising\BulkCosting\BulkCostingController@getSOByStyle');
    Route::apiResource('so-combine', 'Merchandising\CombineSOController');

    Route::apiResource('po-general','Merchandising\PurchaseOrderGeneralController');
    Route::apiResource('po-general-details','Merchandising\PurchaseOrderGeneralDetailsController');

    Route::apiResource('po-manual','Merchandising\PurchaseOrderManualController');
    Route::apiResource('po-manual-details','Merchandising\PurchaseOrderManualDetailsController');

    //Route::get('bulk-costing/validate' , 'Merchandising\BulkCosting\BulkCostingController@validate_data');
    //Route::apiResource('bulk-costing','Merchandising\BulkCosting\BulkCostingController');


  Route::apiResource('po-load','stores\RollPlanController');
  Route::apiResource('roll','stores\RollPlanController');
  /********edited*/
  Route::get('supplier-tolarance/validate' , 'Stores\SupplierTolaranceController@validate_data');
  Route::apiResource('supplier-tolarance','Stores\SupplierTolaranceController');
  Route::apiResource('fabricInspection','stores\FabricInspectionController');
  Route::get('transfer-location/validate' , 'Stores\TransferLocationController@validate_data');
  Route::post('transfer-location-store','Stores\TransferLocationController@storedetails');
  Route::apiResource('transfer-location','Stores\TransferLocationController');
  Route::apiResource('grn', 'Store\GrnController');
  Route::post('save-grn-bin', 'Store\GrnController@saveGrnBins');
  Route::get('load-grn-lines', 'Store\GrnController@loadAddedGrnLInes');
  Route::get('loadPoBinList','Store\StoreBinController@getBinListByLoc');
  Route::get('loadAddedBins','Store\GrnController@getAddedBins');
  Route::get('load-substores','Store\SubStoreController@getSubStoreList');
  Route::get('substore-bin-list','Store\SubStoreController@getSubStoreBinList');
  Route::get('load-bin-qty','Store\BinTransferController@loadBinQty');
  Route::get('load-added-bin-qty','Store\BinTransferController@loadAddedBinQty');
  Route::post('add-bin-qty','Store\BinTransferController@addBinTrnsfer');
  Route::apiResource('save-bin-transfer', 'Store\BinTransferController');

    Route::get('bulk/validate' , 'Merchandising\BulkCosting\BulkDetailsController@validate_data');
    Route::apiResource('bulk','Merchandising\BulkCosting\BulkDetailsController');
    Route::post('po-manual-details/load_bom_Details','Merchandising\PurchaseOrderManualController@load_bom_Details');
    Route::post('po-manual-details/load_reqline','Merchandising\PurchaseOrderManualController@load_reqline');
    Route::post('po-manual-details/load_reqline_2','Merchandising\PurchaseOrderManualDetailsController@load_reqline_2');
    Route::post('po-manual-details/load_por_line','Merchandising\PurchaseOrderManualDetailsController@load_por_line');
    Route::post('po-manual-details/merge_save','Merchandising\PurchaseOrderManualController@merge_save');
    Route::post('po-manual-details/confirm_po','Merchandising\PurchaseOrderManualDetailsController@confirm_po');

    Route::post('po-manual-details/save_line_details','Merchandising\PurchaseOrderManualDetailsController@save_line_details');
    Route::post('po-manual-details/update_line_details','Merchandising\PurchaseOrderManualDetailsController@update_line_details');
    Route::post('po-manual/revision_header' , 'Merchandising\PurchaseOrderManualDetailsController@load_po_revision_header');
    Route::post('po-manual/prl_header_load' , 'Merchandising\PurchaseOrderManualDetailsController@prl_header_load');
    Route::post('po-delivery-split' , 'Merchandising\PurchaseOrderManualDetailsController@po_delivery_split');
    Route::get('po-delivery-split-load' , 'Merchandising\PurchaseOrderManualDetailsController@po_delivery_split_load');


    Route::post('po-manual-details/save_line_details_revision','Merchandising\PurchaseOrderManualDetailsController@save_line_details_revision');
Route::post('po-manual-details/send_to_approval','Merchandising\PurchaseOrderManualDetailsController@send_to_approval');
    //Route::get('bulk-costing-header' , 'Merchandising\BulkCosting\BulkCostingController');
    Route::apiResource('bulk-cost-listing','Merchandising\BulkCosting\BulkCostingController');
    Route::apiResource('bulk-cost-header','Merchandising\BulkCosting\BulkCostingController');

    //Route::get('bom/custorders','Merchandising\BomController@getCustOrders');
    //Route::get('bom/custorderQty','Merchandising\BomController@getCustomerOrderQty');
    //Route::get('bom/assigncustorders','Merchandising\BomController@getAssignCustOrders');

    //Route::get('bom/rmdetails','Merchandising\BomController@getCostingRMDetails');
    //Route::get('bom/bomlist','Merchandising\BomController@ListBOMS');
    //Route::get('bom/bominfolisting','Merchandising\BomController@getBOMDetails');
    //Route::get('bom/bomorderqty','Merchandising\BomController@getBOMOrderQty');
    //Route::get('bom/sizewise','Merchandising\BomController@getSizeWiseDetails');
    //Route::get('bom/colorwise','Merchandising\BomController@getColorWiseDetails');
    //Route::get('bom/both','Merchandising\BomController@getBothRatios');
    //Route::get('bom/colorcombolist','Merchandising\BomController@getColorCombo');
    //Route::get('bom/getratio','Merchandising\BomController@getMatRatio');
    //Route::get('bom/getsalesorder','Merchandising\BomController@getAssignSalesOrder');

  //  Route::post('bom/savebomheader','Merchandising\BomController@saveBOMHeader');
  //  Route::post('bom/savebomdetail','Merchandising\BomController@saveBOMDetails');
  //  Route::post('bom/savesoallocation','Merchandising\BomController@saveSOAllocation');
  //  Route::post('bom/savesmaterialratio','Merchandising\BomController@saveMaterialRatio');

    //Route::post('bom/ratio/save','Merchandising\BomController@saveMeterialRatio');
    Route::post('bom/save-item','Merchandising\BomController@save_item');
    Route::post('bom/save-items','Merchandising\BomController@save_items');
    Route::post('bom/remove-item','Merchandising\BomController@remove_item');
    Route::post('bom/copy-item','Merchandising\BomController@copy_item');
    Route::post('bom/edit-mode', 'Merchandising\BomController@edit_mode');
    Route::post('bom/confirm-bom', 'Merchandising\BomController@confirm_bom');
    Route::post('bom/send-for-approval', 'Merchandising\BomController@send_for_approval');
    Route::post('bom/copy-all-items-from', 'Merchandising\BomController@copy_all_items_from');
    Route::apiResource('bom','Merchandising\BomController');

    Route::post('items/check_and_generate_item_description','Merchandising\Item\ItemController@check_and_generate_item_description');
    Route::post('items/create-inventory-items', 'Merchandising\Item\ItemController@create_inventory_items');
    Route::post('items/load_item_edit', 'Merchandising\Item\ItemController@load_item_edit');
    Route::post('items/update_item_edit', 'Merchandising\Item\ItemController@update_item_edit');
    Route::apiResource('items','Merchandising\Item\ItemController');

    Route::apiResource('item-categories','Merchandising\Item\CategoryController');
    Route::apiResource('item-sub-categories','Merchandising\Item\SubCategoryController');
    Route::apiResource('item-content-types','Merchandising\Item\ContentTypeController');
    Route::apiResource('item-compositions','Merchandising\Item\CompositionController');
    Route::get('item-properties/validate','Merchandising\Item\ItemPropertyController@validate_data');
    Route::apiResource('item-properties','Merchandising\Item\ItemPropertyController');
    Route::apiResource('item-property-values','Merchandising\Item\ItemPropertyValueController');

    Route::post('load_un_assign_list', 'Merchandising\Item\ItemPropertyController@load_un_assign_list');
    Route::post('load_un_assign_list2', 'Merchandising\Item\ItemPropertyController@load_un_assign_list2');
    Route::post('save_assign', 'Merchandising\Item\ItemPropertyController@save_assign');
    Route::post('final_save_assign', 'Merchandising\Item\ItemPropertyController@final_save_assign');
    Route::post('save_pro_name', 'Merchandising\Item\ItemPropertyController@save_pro_name');
    Route::post('remove_assign', 'Merchandising\Item\ItemPropertyController@remove_assign');
    Route::post('remove_unassign', 'Merchandising\Item\ItemPropertyController@remove_unassign');


    Route::apiResource('pro-silhouette','Merchandising\ProductSilhouetteController');
    Route::post('save_product_feature','Merchandising\ProductFeatureController@save_product_feature');
    Route::post('pro_listload_edit', 'Merchandising\ProductFeatureController@pro_listload_edit');
    Route::apiResource('product_feature','Merchandising\ProductFeatureController');
    Route::post('update_product_feature','Merchandising\ProductFeatureController@update_product_feature');
    Route::post('save_line_fe', 'Merchandising\ProductFeatureController@save_line_fe');
    Route::post('delete_feature_temp','Merchandising\ProductFeatureController@delete_feature_temp');


    //Route::post('bom/setzeromaterialratio','Merchandising\BomController@clearMatRatio');

    Route::get('buy-master/validate' , 'Merchandising\BuyMasterController@validate_data');
    Route::apiResource('buy-master','Merchandising\BuyMasterController');


    Route::apiResource('shop-orders','Merchandising\ShopOrderController');
    Route::post('load_shop_order_header' , 'Merchandising\ShopOrderController@load_shop_order_header');
    Route::post('load_shop_order_list' , 'Merchandising\ShopOrderController@load_shop_order_list');
    Route::post('update_shop_order_details','Merchandising\ShopOrderController@update_shop_order_details');

    Route::apiResource('shop_order','Merchandising\ShopOrderController');

});

Route::prefix('admin/')->group(function(){

    Route::get('users/roles','Admin\UserController@roles');
    Route::post('users/roles','Admin\UserController@save_roles');
    Route::get('users/locations','Admin\UserController@locations');
    Route::post('users/locations','Admin\UserController@save_locations');
    Route::get('users/user-assigned-locations','Admin\UserController@user_assigned_locations');
    Route::get('users/validate', 'Admin\UserController@validate_data');
    Route::apiResource('users','Admin\UserController');


    Route::get('permission/validate' , 'Admin\PermissionController@validate_data');
    Route::apiResource('permission','Admin\PermissionController');

    Route::get('roles/validate' , 'Admin\RoleController@validate_data');
    Route::post('roles/change-role-permission','Admin\RoleController@change_role_permission');
    Route::apiResource('roles','Admin\RoleController');

    Route::apiResource('permission-categories','Admin\PermissionCategoryController');
    Route::apiResource('permissions','Admin\PermissionController');

  Route::get('approval-stages/validate' , 'Admin\ApprovalStageController@validate_data');
  Route::apiResource('approval-stages','Admin\ApprovalStageController');

  Route::apiResource('process-approvals','Admin\ProcessApprovalController');


});

Route::prefix('store/')->group(function(){

    Route::auth();

    Route::get('stores/validate' , 'Store\StoreController@validate_data');
    Route::apiResource('stores','Store\StoreController');

    Route::get('storebin/validate' , 'Store\StoreBinController@validate_data');
    Route::apiResource('storebin','Store\StoreBinController');

    Route::get('substore/validate' , 'Store\SubStoreController@validate_data');
    Route::apiResource('substore','Store\SubStoreController');

    Route::get('mat-trans-in/validate' , 'Store\MaterialTransferInController@validate_data');
    Route::apiResource('mat-trans-in','Store\MaterialTransferInController');


    Route::apiResource('fabricInspection','Store\FabricInspectionController');


    Route::get('bin-config/validate' , 'Store\BinConfigController@validate_data');
    Route::apiResource('bin-config','Store\BinConfigController');
    Route::get('mrn/loadDetails','Store\MrnController@loadDetails');
    Route::apiResource('mrn','Store\MrnController');
    Route::post('mrn/filterData','Store\MrnController@filterData');
    Route::post('issue/confirm-issue-data','Store\IssueController@confirmIssueData');
    Route::apiResource('issue','Store\IssueController');
    Route::apiResource('roll','Store\RollPlanController');
    Route::apiResource('trimPacking','Store\TrimPackingController');
    Route::get('loadMrnData','Store\IssueController@loadMrnData');
    Route::get('loadBinDetails','Store\IssueController@loadBinDetails');

    Route::apiResource('return-to-stores','Store\ReturnToStoresController');
    Route::post('load_issue_details','Store\ReturnToStoresController@load_issue_details');

    Route::apiResource('return-to-supplier','Store\ReturnToSupplierController');
    Route::post('load_grn_details','Store\ReturnToSupplierController@load_grn_details');
    Route::post('load_grn_header','Store\ReturnToSupplierController@load_grn_header');

    Route::apiResource('bin-to-bin-transfer','Store\BinToBinTransferController');
    Route::post('load_bin_items','Store\BinToBinTransferController@load_bin_items');
    Route::post('load_sub_store_bin','Store\BinToBinTransferController@load_sub_store_bin');

});


Route::prefix('core/')->group(function(){

    Route::apiResource('status','Core\StatusController');

});

Route::prefix('manufacturing/')->group(function(){

    Route::apiResource('prod-order','Manufacturing\ProdOrderController');

});


Route::prefix('app/')->group(function(){


  Route::get('users/roles','Admin\UserController@roles');
  Route::post('users/roles','Admin\UserController@save_roles');
  Route::get('users/locations','Admin\UserController@locations');
  Route::post('users/locations','Admin\UserController@save_locations');
  Route::get('users/user-assigned-locations','Admin\UserController@user_assigned_locations');
  Route::apiResource('users','Admin\UserController');


    Route::GET('menus','App\MenuController@index');
    //search menu
    Route::POST('search_menu','App\MenuController@getSearchMenu');
    Route::POST('search','App\SearchController@index');
    Route::apiResource('permissions','App\PermissionController');
    Route::apiResource('bookmarks', 'App\BookmarkController')->only(['index', 'store']);

});


Route::prefix('approval-process/')->group(function(){

    Route::get('start','App\ApprovalController@start');
    Route::get('read','App\ApprovalController@approve');
    Route::get('generate_costing_bom','App\ApprovalController@generate_costing_bom');
    Route::get('remove_costing_data','App\ApprovalController@remove_costing_data');
});

Route::prefix('dashboard/')->group(function(){
    Route::apiResource('dashboard', 'DashBoard\DashBoardController');
});
//Route::group(['middleware' => ['jwt.auth']], function() {


  Route::GET('menus','App\MenuController@index');
  Route::POST('search','App\SearchController@index');
  Route::POST('required-permissions','App\PermissionController@get_required_permissions');
  Route::apiResource('bookmarks', 'App\BookmarkController')->only(['index', 'store']);


Route::GET('/sources','Test\SourceController@index');
Route::GET('/getCustomer','Org\CustomerController@loadCustomer');
Route::GET('/getProductCategory','Merchandising\ProductCategoryController@loadProductCategory');
Route::GET('/getProductType','Merchandising\ProductTypeController@loadProductType');
Route::GET('/getProductFeature','Merchandising\ProductFeatureController@loadProductFeature');
Route::GET('/getProductSilhouette','Merchandising\ProductSilhouetteController@loadProductSilhouette');
Route::GET('/getProductSilhouetteHome','Merchandising\ProductSilhouetteController@loadProductSilhouetteHome');

Route::POST('/style-creation.save','Merchandising\StyleCreationController@saveStyleCreation');
Route::get('/loadstyles','Merchandising\StyleCreationController@loadStyles');
Route::get('/loadStyleDetails','Merchandising\StyleCreationController@GetStyleDetails');

Route::get('/seasonlist' , 'Org\SeasonController@GetSeasonsList');
Route::apiResource('seasons','Org\SeasonController');

Route::post('flashcosting/savecostingheader', 'Merchandising\Costing\Flash\FlashController@saveCostingHeader');
Route::post('flashcosting/savecostingdetails', 'Merchandising\Costing\Flash\FlashController@saveCostingDetails');

Route::post('flashcosting/confirmcosting', 'Merchandising\Costing\Flash\FlashController@confirmCostSheet');
Route::post('flashcosting/revisecosting', 'Merchandising\Costing\Flash\FlashController@reviseCostSheet');
Route::post('flashcosting/setinactive', 'Merchandising\Costing\Flash\FlashController@setItemInactive');

Route::get('flashcosting/listcosting', 'Merchandising\Costing\Flash\FlashController@ListingCostings');
Route::get('flashcosting/listcostingheader', 'Merchandising\Costing\Flash\FlashController@getCostingHeader');
Route::get('flashcosting/listcostinglines', 'Merchandising\Costing\Flash\FlashController@getCostingLines');
Route::get('flashcosting/getcostitems', 'Merchandising\Costing\Flash\FlashController@getCostingItems');

/*Route::post('/sources','Test\SourceController@index');

  Route::get('logout', 'AuthController@logout');
  Route::get('test', function(){
      return response()->json(['foo'=>'bar']);
  });*/
//});

  Route::GET('/sources','Test\SourceController@index');
  Route::GET('/getCustomer','Org\CustomerController@loadCustomer');
  Route::GET('/getProductCategory','Merchandising\ProductCategoryController@loadProductCategory');
  Route::GET('/getProductType','Merchandising\ProductTypeController@loadProductType');
  Route::GET('/getProductFeature','Merchandising\ProductFeatureController@loadProductFeature');
  Route::GET('/getProductSilhouette','Merchandising\ProductSilhouetteController@loadProductSilhouette');
  //Route::GET('/getDivision','Org\CustomerController@loadCustomerDivision');

Route::GET('/getDivision','Org\CustomerController@loadCustomerDivision');



Route::prefix('reports/')->group(function(){
  //Sales Report
  Route::apiResource('load_sales','Reports\SalesReportController');
  //PO Report
  Route::post('load_po_details','Reports\POReportController@load_po_details');
  Route::apiResource('load_po','Reports\POReportController');
  Route::apiResource('load_status','Reports\POReportController');
  //Costing Report
  Route::apiResource('costing/costing_details','Reports\CostingReportController');
  //Costing Details Report
  Route::get('view-costing','Reports\CostingReportController@viewCostingDetails');
  //Costing variance Report
  Route::get('view-costing-variance','Reports\CostingReportController@viewCostingVersionDetails');
  //Inventory ageing Report
  Route::apiResource('inv-ageing','Reports\InvAgeingReportController');
  //Pick List
  Route::apiResource('load_pick_list','Reports\PickListController');
  Route::post('update-issue-status','Reports\PickListController@update_issue_status');

  //Costing variance Report
  Route::get('view-pick_list','Reports\PickListController@viewPickList');
  //Barcode printing
  Route::post('fabric_roll_barcode_print','Reports\FabticRollBarcode@getData');
  Route::post('update_print_status','Reports\FabticRollBarcode@updatePrint');
  Route::post('delete_barcode','Reports\FabticRollBarcode@deleteBarcode');
  //Style list report
  Route::post('style-list','Reports\StyleListController@getStyles');
  //MSR Report
  Route::apiResource('load_msr','Reports\MSRReportController');
  Route::apiResource('load_shop_order','Reports\MSRReportController');
  //Issue Report
  Route::apiResource('load_issue','Reports\IssueReportController');

  //MRN note
  Route::apiResource('load_mrn','Reports\MRNNoteController');
  Route::get('load_mrn_note','Reports\MRNNoteController@getMrnNote');

  //Daily Receiving Reports
  Route::apiResource('load_inward','Reports\DailyRecReportController');

  //Inventory scarp header report
  Route::apiResource('load_scarp_header','Reports\InventoryScarpController');
  Route::post('load_scarp_details','Reports\InventoryScarpController@load_inventory');

  //Inventory Part In Stock Report
  Route::apiResource('load_inv_part','Reports\InventoryPartInStockController');

  //po details report
  Route::apiResource('load_po_details','Reports\PODetailsController');
  Route::get('view-po-details','Reports\PODetailsController@viewPODetails');


  Route::apiResource('eject_stock','Reports\InventoryScarpController');
  //Daily Receiving Reports
  Route::apiResource('load_inward','Reports\DailyRecReportController');

  //BOM report
  Route::apiResource('load_bom_report','Reports\BOMReportController');
  Route::get('view-bom','Reports\BOMReportController@view_bom');

});

Route::prefix('common/')->group(function(){
  Route::apiResource('load_costing_id','Reports\CommonController');
  Route::apiResource('user_locations','Reports\CommonController');
  Route::apiResource('load_item_code','Reports\CommonController');
  Route::apiResource('load_item_code','Reports\CommonController');
  Route::apiResource('load_fng_code','Reports\CommonController');
  Route::post('load_advance_parameters','Reports\CommonController@load_advance_parameters');
});
