<?php

/*
  |--------------------------------------------------------------------------
  | Web Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register web routes for your application. These
  | routes are loaded by the RouteServiceProvider within a group which
  | contains the "web" middleware group. Now create something great!
  |
 */

//login
Route::get('/', 'Auth\LoginController@showLogin');

Route::post('login', array('uses' => 'Auth\LoginController@doLogin'));

Route::get('logout', 'Auth\LoginController@logout');

Route::get('reset', 'Auth\ForgotPasswordController@reset');

Route::get('select-location', 'Auth\LoginController@selectLocation');

Route::POST('loginWithLoc', 'Auth\LoginController@loginWithLoc');

Route::GET('menus','App\MenuController@index');


Route::get('/recover', function () {
    return view('recover');
});

Route::get('/home', function () {
    return view('dashboard');
});

Route::get('/icon', function () {
    return view('icon_page');
});
Route::get('alert', function () {
	return view('alert');
});


Route::get('costing', function () {
	return view('costing');
});

//country routes
/*Route::prefix('org/country/')->group(function () {
  Route::get('add_country','CountryController@index');
  Route::get('list','CountryController@loaddata');
  Route::get('check-code','CountryController@check_code');
  Route::post('save','CountryController@saveCountry');
  Route::get('get','CountryController@edit');
  Route::get('change-status','CountryController@delete');
  Route::get('active_list','CountryController@get_active_list');
});*/


//Division module
/*Route::prefix('org/division/')->group(function () {
  Route::get('add_division','DivisionController@index');
  Route::get('check-code','DivisionController@check_code');
  Route::get('list','DivisionController@loadData');
  Route::post('save','DivisionController@saveDivision');
  Route::get('get','DivisionController@edit');
  Route::get('change-status','DivisionController@delete');
});*/


//Season module
/*Route::get('add_season','SeasonController@index');
Route::get('check_season_code','SeasonController@checkCode');
Route::get('get_all_season','SeasonController@loadData');
Route::post('save_season','SeasonController@saveSeason');
Route::get('edit_season','SeasonController@edit');
Route::get('delete_season','SeasonController@delete');*/

//UOM module
/*Route::get('add_uom','UomController@index');
Route::get('check_uom_code','UomController@checkCode');
Route::get('get_all_uom','UomController@loadData');
Route::post('save_uom','UomController@saveUom');
Route::get('edit_uom','UomController@edit');
Route::get('delete_uom','UomController@delete');*/
//Section module
/*Route::get('add_section','SectionController@index');
Route::get('check_section_code','SectionController@checkCode');
Route::get('get_all_section','SectionController@loadData');
Route::post('save_section','SectionController@saveSection');
Route::get('edit_section','SectionController@edit');
Route::get('delete_section','SectionController@delete');*/

/*Route::prefix('org/season/')->group(function () {
  Route::get('add_season','SeasonController@index');
  Route::get('check-code','SeasonController@check_code');
  Route::get('list','SeasonController@loadData');
  Route::post('save','SeasonController@saveSeason');
  Route::get('get','SeasonController@edit');
  Route::get('change-status','SeasonController@delete');
});*/

//UOM module
/*Route::prefix('org/uom/')->group(function () {
  Route::get('add_uom','UomController@index');
  Route::get('check-code','UomController@check_code');
  Route::get('list','UomController@loadData');
  Route::post('save','UomController@saveUom');
  Route::get('get','UomController@edit');
  Route::get('delete','UomController@delete');
  Route::get('list-all','UomController@LoadUOM');
});*/

//Section module
/*Route::prefix('org/section/')->group(function(){
  Route::get('add_section','SectionController@index');
  Route::get('check-code','SectionController@check_code');
  Route::get('list','SectionController@loadData');
  Route::post('save','SectionController@saveSection');
  Route::get('get','SectionController@edit');
  Route::get('delete','SectionController@delete');
});*/

//Cancellation category module
/*Route::get('add_category', 'Org\Cancellation\CancellationCategoryController@index');
Route::get('check_category_code', 'Org\Cancellation\CancellationCategoryController@checkCode');
Route::get('get_all_category', 'Org\Cancellation\CancellationCategoryController@loadData');
Route::post('save_category', 'Org\Cancellation\CancellationCategoryController@saveCategory');
Route::get('edit_category', 'Org\Cancellation\CancellationCategoryController@edit');
Route::get('delete_category', 'Org\Cancellation\CancellationCategoryController@delete');*/

//Cancellation Reason module
/*Route::get('add_reason', 'Org\Cancellation\CancellationReasonController@index');
Route::get('check_reason_code', 'Org\Cancellation\CancellationReasonController@checkCode');
Route::get('get_all_reason', 'Org\Cancellation\CancellationReasonController@loadData');
Route::post('save_reason', 'Org\Cancellation\CancellationReasonController@saveReason');
Route::get('edit_reason', 'Org\Cancellation\CancellationReasonController@edit');
Route::get('delete_reason', 'Org\Cancellation\CancellationReasonController@delete');*/

//product type Module
/*Route::get('add_product_type', 'Org\ProductTypeController@index');
Route::get('check_product_type_code', 'Org\ProductTypeController@checkCode');
Route::get('get_all_product_type', 'Org\ProductTypeController@loadData');
Route::post('save_product_type', 'Org\ProductTypeController@saveProduct');
Route::get('edit_product_type', 'Org\ProductTypeController@edit');
Route::get('delete_product_type', 'Org\ProductTypeController@delete');*/

//sample stage Module
Route::get('add_sample_stage', 'Org\SampleStageController@index');
Route::get('check_sample_stage_code', 'Org\SampleStageController@checkCode');
Route::get('get_all_sample_stage', 'Org\SampleStageController@loadData');
Route::post('save_sample_stage', 'Org\SampleStageController@saveStage');
Route::get('edit_sample_stage', 'Org\SampleStageController@edit');
Route::get('delete_sample_stage', 'Org\SampleStageController@delete');

//customer creation module
/*Route::get('create_customer', 'Org\CustomerController@index');
Route::get('check_customer_code', 'Org\CustomerController@checkCode');
Route::get('get_all_customers', 'Org\CustomerController@loadData');
Route::post('save_customer', 'Org\CustomerController@saveCustomer');
Route::get('edit_customer', 'Org\CustomerController@edit');
Route::get('delete_customer', 'Org\CustomerController@delete');
Route::get('load_currency', 'Org\CustomerController@loadCurrency');
Route::get('load_payemnt_terms', 'Org\CustomerController@loadPayemntTerms');*/
//Route::get('Mainlocation.load_country', 'Org\Location\MainLocationController@load_country');
//DESIGN SHEET & STYLE BRIEF
Route::get('create_style', 'Org\DesignAndStyleController@index');
//Item Creation
Route::get('create_item', 'Org\ItemController@index');


//GRN module
Route::get('grn_details', 'GrnController@grnDetails');

//User Management

Route::get('register', 'Admin\\UserController@register');
Route::post('register-user', 'Admin\\UserController@store');
Route::get('validate-empno', 'Admin\\UserController@validateEmpNo');

/*Route::get('admin/register', 'Admin\\UserController@register');
Route::get('admin/user', 'Admin\\UserController@user');
Route::post('admin/register-user', 'Admin\\UserController@store');
Route::get('admin/validate-empno', 'Admin\\UserController@validateEmpNo');
Route::get('admin/validate-username', 'Admin\\UserController@validateUserName');
Route::get('admin/load-report-levels', 'Admin\\UserController@loadReportLevels');
Route::post('admin/get-user-list', 'Admin\\UserController@getUserList');*/



//currency routes
/*Route::get('currency.new',['uses' => 'CurrencyController@new_currency']);
Route::post('currency.save','CurrencyController@save');
Route::get('currency.get_currency_list','CurrencyController@get_currency_list');
Route::get('org/currency/active-list','CurrencyController@get_active_list');
Route::get('currency.get','CurrencyController@get_currency');*/

//Route::get('accounting-rules', function () { return view('finance/accounting/accounting_rules'); });


//Route::get('accounting-rules', function () { return view('finance/accounting/accounting_rules'); });

//payment term routes

//Route::get('payment-term.new','PaymentTermController@new_payment_term');
/*Route::get('payment-term-check-code','Finance\Accounting\PaymentTermController@check_perment_term_code');

Route::post('payment-term.save','Finance\Accounting\PaymentTermController@save');

Route::get('payment-term.get_payment_term_list','Finance\Accounting\PaymentTermController@get_payment_term_list');

Route::get('payment-term.get','Finance\Accounting\PaymentTermController@get_payment_term');

Route::get('payment-term-change-status','Finance\Accounting\PaymentTermController@change_status');*/

//Payment method
/*Route::get('payment-method-check-code','Finance\Accounting\PaymentMethodController@check_perment_method_code');

Route::post('payment-method.save','Finance\Accounting\PaymentMethodController@save');*/


/*Route::get('payment-method.get_payment_method_list','Finance\Accounting\PaymentMethodController@get_payment_method_list');

Route::get('payment-method.get','Finance\Accounting\PaymentMethodController@get_payment_method');

Route::get('payment-method-change-status','Finance\Accounting\PaymentMethodController@change_status');*/

//cost center routes
//Route::get('cost-center.new','Finance\Accounting\CostCenterController@new');
//Route::get('cost-center-check-code','Finance\Accounting\CostCenterController@check_cost_center_code');

//Route::post('cost-center.save','Finance\Accounting\CostCenterController@save');

//Route::get('cost-center.get_list','Finance\Accounting\CostCenterController@get_list');

//Route::get('cost-center.get','Finance\Accounting\CostCenterController@get');

//Route::get('payment-term.new','Finance\Accounting\PaymentTermController@new_payment_term');
/*Route::get('finance/accounting/payment-term/check-code','Finance\Accounting\PaymentTermController@check_code');
Route::post('finance/accounting/payment-term/save','Finance\Accounting\PaymentTermController@save');
Route::get('finance/accounting/payment-term/list','Finance\Accounting\PaymentTermController@get_list');
Route::get('finance/accounting/payment-term/get','Finance\Accounting\PaymentTermController@get_payment_term');
Route::get('finance/accounting/payment-term/change-status','Finance\Accounting\PaymentTermController@change_status');*/

//payment term routes
//Route::get('payment-method.new','Finance\Accounting\PaymentMethodController@new_payment_term');
/*Route::post('finance/accounting/payment-method/save','Finance\Accounting\PaymentMethodController@save');
Route::get('finance/accounting/payment-method/check-code','Finance\Accounting\PaymentMethodController@check_code');
Route::get('finance/accounting/payment-method/list','Finance\Accounting\PaymentMethodController@get_list');
Route::get('finance/accounting/payment-method/get','Finance\Accounting\PaymentMethodController@get_payment_method');
Route::get('finance/accounting/payment-method/change-status','Finance\Accounting\PaymentMethodController@change_status');*/

//cost center routes
//Route::get('cost-center.new','Finance\Accounting\CostCenterController@new');
/*Route::post('finance/accounting/cost-center/save','Finance\Accounting\CostCenterController@save');
Route::get('finance/accounting/cost-center/check-code','Finance\Accounting\CostCenterController@check_code');
Route::get('finance/accounting/cost-center/list','Finance\Accounting\CostCenterController@get_list');
Route::get('finance/accounting/cost-center/get','Finance\Accounting\CostCenterController@get');
Route::get('finance/accounting/cost-center/change-status','Finance\Accounting\CostCenterController@change_status');
Route::get('finance/accounting/cost-center/active_list','Finance\Accounting\CostCenterController@get_active_list');*/


//Route::get('cost-center-change-status','Finance\Accounting\CostCenterController@change_status');


//origin type routes
/*Route::get('origin-type-new','Org\OriginTypeController@new');
Route::get('origin-type-check-code','Org\OriginTypeController@check_origin_type');
Route::post('origin-type-save','Org\OriginTypeController@save');
Route::get('origin-type-get-list','Org\OriginTypeController@get_list');
Route::get('origin-type-get','Org\OriginTypeController@get');
Route::get('origin-type-change-status','Org\OriginTypeController@change_status');*/

/*Route::get('add_location', function () { return view('org/location/add_location'); });

Route::post('Mainsource.postdata','Org\Location\MainSourceController@postdata');
Route::get('Mainsource.loaddata','Org\Location\MainSourceController@loaddata');
Route::get('Mainsource.check_code','Org\Location\MainSourceController@check_code');
Route::get('Mainsource.edit','Org\Location\MainSourceController@edit');
Route::get('Mainsource.delete','Org\Location\MainSourceController@delete');
Route::get('Mainsource.load_list','Org\Location\MainSourceController@select_Source_list');

Route::get('Maincluster.loaddata','Org\Location\MainClusterController@loaddata');
Route::get('Maincluster.check_code','Org\Location\MainClusterController@check_code');
Route::post('Maincluster.postdata','Org\Location\MainClusterController@postdata');
Route::get('Maincluster.edit','Org\Location\MainClusterController@edit');
Route::get('Maincluster.delete','Org\Location\MainClusterController@delete');

Route::get('Mainlocation.loaddata','Org\Location\MainLocationController@loaddata');
Route::get('Mainlocation.load_list','Org\Location\MainLocationController@select_loc_list');
Route::get('Mainlocation.load_currency','Org\Location\MainLocationController@load_currency');
Route::get('Mainlocation.load_country','Org\Location\MainLocationController@load_country');
Route::get('Mainlocation.check_code','Org\Location\MainLocationController@check_code');
Route::post('Mainlocation.postdata','Org\Location\MainLocationController@postdata');
Route::get('Mainlocation.edit','Org\Location\MainLocationController@edit');
Route::get('Mainlocation.delete','Org\Location\MainLocationController@delete');

Route::get('MainSubLocation.loaddata','Org\Location\MainSubLocationController@loaddata');
Route::get('MainSubLocation.load_list','Org\Location\MainSubLocationController@load_list');
Route::get('MainSubLocation.check_code','Org\Location\MainSubLocationController@check_code');
Route::post('MainSubLocation.postdata','Org\Location\MainSubLocationController@postdata');
Route::get('MainSubLocation.edit','Org\Location\MainSubLocationController@edit');
Route::get('MainSubLocation.delete','Org\Location\MainSubLocationController@delete');*/


// add location

/*Route::get('add_location', function () { return view('org/location/add_location'); });

Route::post('Mainsource.postdata','Org\Location\MainSourceController@postdata');
Route::get('Mainsource.loaddata','Org\Location\MainSourceController@loaddata');
Route::get('Mainsource.check_code','Org\Location\MainSourceController@check_code');
Route::get('Mainsource.edit','Org\Location\MainSourceController@edit');
Route::get('Mainsource.delete','Org\Location\MainSourceController@delete');
Route::get('Mainsource.load_list','Org\Location\MainSourceController@select_Source_list');

Route::get('Maincluster.loaddata','Org\Location\MainClusterController@loaddata');
Route::get('Maincluster.check_code','Org\Location\MainClusterController@check_code');
Route::post('Maincluster.postdata','Org\Location\MainClusterController@postdata');
Route::get('Maincluster.edit','Org\Location\MainClusterController@edit');
Route::get('Maincluster.delete','Org\Location\MainClusterController@delete');*/

/*Route::get('Mainlocation.loaddata','Org\Location\MainLocationController@loaddata');
Route::get('Mainlocation.load_list','Org\Location\MainLocationController@select_loc_list');
Route::get('Mainlocation.load_currency','Org\Location\MainLocationController@load_currency');
Route::get('Mainlocation.load_country','Org\Location\MainLocationController@load_country');
Route::get('Mainlocation.check_code','Org\Location\MainLocationController@check_code');
Route::post('Mainlocation.postdata','Org\Location\MainLocationController@postdata');
Route::get('Mainlocation.edit','Org\Location\MainLocationController@edit');
Route::get('Mainlocation.delete','Org\Location\MainLocationController@delete');*/

/*Route::get('MainSubLocation.loaddata','Org\Location\MainSubLocationController@loaddata');
Route::get('MainSubLocation.load_list','Org\Location\MainSubLocationController@load_list');
Route::get('MainSubLocation.check_code','Org\Location\MainSubLocationController@check_code');
Route::post('MainSubLocation.postdata','Org\Location\MainSubLocationController@postdata');
Route::get('MainSubLocation.edit','Org\Location\MainSubLocationController@edit');
Route::get('MainSubLocation.delete','Org\Location\MainSubLocationController@delete');*/

//Route::get('add_location', function () { return view('org/location/add_location'); });
//Source routes
/*Route::post('org/source/save','Org\Location\SourceController@save');
Route::get('org/source/list','Org\Location\SourceController@get_list');
Route::get('org/source/check-code','Org\Location\SourceController@check_code');
Route::get('org/source/edit','Org\Location\SourceController@edit');
Route::get('org/source/change-status','Org\Location\SourceController@change_status');
Route::get('org/source/active-list','Org\Location\SourceController@get_active_source_list');
Route::get('org/source/change-status','Org\Location\SourceController@change_status');*/

//cluster routes
/*Route::post('org/cluster/save','Org\Location\ClusterController@save');
Route::get('org/cluster/list','Org\Location\ClusterController@get_list');
Route::get('org/cluster/check-code','Org\Location\ClusterController@check_code');
Route::get('org/cluster/get','Org\Location\ClusterController@get');
Route::get('org/cluster/change-status','Org\Location\ClusterController@change_status');
Route::get('org/cluster/active_list','Org\Location\ClusterController@get_active_list');*/

/*Route::get('org/company/list','Org\Location\CompanyController@get_list');
Route::get('org/company/check-code','Org\Location\CompanyController@check_code');
Route::post('org/company/save','Org\Location\CompanyController@save');
Route::get('org/company/get','Org\Location\CompanyController@get_company');
Route::get('org/company/depat_list','Org\Location\CompanyController@load_depat_list');
Route::get('org/company/section-list','Org\Location\CompanyController@load_section_list');
Route::get('org/company/change-status','Org\Location\CompanyController@change_status');
Route::get('org/company/active_list','Org\Location\CompanyController@get_active_list');*/
//Route::get('org/company/test','Org\Location\CompanyController@test');


//Route::post('Mainlocation.save_section','Org\Location\MainLocationController@save_section');
//Route::get('Mainlocation.section','Org\Location\MainLocationController@edit_load_section');
//Route::get('Mainlocation.load_currency','Org\Location\CompanyController@load_currency');
//Route::get('Mainlocation.load_country','Org\Location\CompanyController@load_country');
//Route::get('MainSubLocation.type_of_loc','Org\Location\MainSubLocationController@type_of_loc');

//routes for location
/*Route::get('org/location/list','Org\Location\LocationController@get_list');
Route::get('org/location/check-code','Org\Location\LocationController@check_code');
Route::post('org/location/save','Org\Location\LocationController@save');
Route::get('org/location/get','Org\Location\LocationController@get_location');
Route::get('org/location/change-status','Org\Location\LocationController@change_status');*/

//Route::get('MainSubLocation.load_cost_center','Org\Location\MainSubLocationController@load_cost_center');
//Route::get('MainSubLocation.load_property','Org\Location\MainSubLocationController@load_property');

//property type routes
/*Route::get('org/property-type/list','Org\PropertyTypeController@get_list');
Route::get('org/property-type/active_list','Org\PropertyTypeController@get_active_list');*/

//location type routes
/*Route::get('org/location-type/list','Org\LocationTypeController@get_list');
Route::get('org/location-type/active_list','Org\LocationTypeController@get_active_list');*/

/*Route::post('Mainsource.postdata', 'Org\Location\MainSourceController@postdata');
Route::get('Mainsource.loaddata', 'Org\Location\MainSourceController@loaddata');
Route::get('Mainsource.check_code', 'Org\Location\MainSourceController@check_code');
Route::get('Mainsource.edit', 'Org\Location\MainSourceController@edit');
Route::get('Mainsource.delete', 'Org\Location\MainSourceController@delete');
Route::get('Mainsource.load_list', 'Org\Location\MainSourceController@select_Source_list');

Route::get('Maincluster.loaddata', 'Org\Location\MainClusterController@loaddata');
Route::get('Maincluster.check_code', 'Org\Location\MainClusterController@check_code');
Route::post('Maincluster.postdata', 'Org\Location\MainClusterController@postdata');
Route::get('Maincluster.edit', 'Org\Location\MainClusterController@edit');
Route::get('Maincluster.delete', 'Org\Location\MainClusterController@delete');

Route::get('Mainlocation.loaddata', 'Org\Location\MainLocationController@loaddata');
Route::get('Mainlocation.load_list', 'Org\Location\MainLocationController@select_loc_list');
Route::get('Mainlocation.load_currency', 'Org\Location\MainLocationController@load_currency');
Route::get('Mainlocation.load_country', 'Org\Location\MainLocationController@load_country');
Route::get('Mainlocation.check_code', 'Org\Location\MainLocationController@check_code');
Route::post('Mainlocation.postdata', 'Org\Location\MainLocationController@postdata');
Route::get('Mainlocation.edit', 'Org\Location\MainLocationController@edit');
Route::get('Mainlocation.delete', 'Org\Location\MainLocationController@delete');
Route::get('Mainlocation.load_section_list', 'Org\Location\MainLocationController@load_section_list');
Route::post('Mainlocation.save_section', 'Org\Location\MainLocationController@save_section');
Route::get('Mainlocation.section', 'Org\Location\MainLocationController@edit_load_section');
Route::get('Mainlocation.load_depat_list', 'Org\Location\MainLocationController@load_depat_list');

Route::get('MainSubLocation.loaddata', 'Org\Location\MainSubLocationController@loaddata');
Route::get('MainSubLocation.load_list', 'Org\Location\MainSubLocationController@load_list');
Route::get('MainSubLocation.check_code', 'Org\Location\MainSubLocationController@check_code');
Route::post('MainSubLocation.postdata', 'Org\Location\MainSubLocationController@postdata');
Route::get('MainSubLocation.edit', 'Org\Location\MainSubLocationController@edit');
Route::get('MainSubLocation.delete', 'Org\Location\MainSubLocationController@delete');
Route::get('MainSubLocation.type_of_loc', 'Org\Location\MainSubLocationController@type_of_loc');
Route::get('MainSubLocation.load_cost_center', 'Org\Location\MainSubLocationController@load_cost_center');
Route::get('MainSubLocation.load_property', 'Org\Location\MainSubLocationController@load_property');*/
// close add location
// Department
/*Route::get('department', function () { return view('org/department/department'); });
Route::post('org/department/save','Org\DepartmentController@save_dep');
Route::get('org/department/check-code','Org\DepartmentController@check_code');
Route::get('org/department/list','Org\DepartmentController@loaddata');
Route::get('org/department/get','Org\DepartmentController@edit');
Route::get('org/department/change-status','Org\DepartmentController@delete');*/


// supplier
/*Route::get('supplier', 'SupplierController@view');
Route::post('supplier/getList', 'SupplierController@getList');
Route::post('supplier/save', 'SupplierController@saveSupplier');*/

//Route::get('supplier/edit', 'SupplierController@loadEditSupplier');
/*Route::get('supplier/loadAddOrEdit', 'SupplierController@loadAddEditSupplier');
Route::get('supplier/delete', 'SupplierController@deleteSupplier');*/


/*Route::resource('admin/permission', 'Admin\PermissionController');
Route::get('admin/role/checkName', 'Admin\RoleController@checkName');
Route::post('admin/role/getList', 'Admin\RoleController@getList');
Route::post('admin/role/{id}', 'Admin\RoleController@update');
Route::delete('admin/role/{id}', 'Admin\RoleController@destroy');
Route::resource('admin/role', 'Admin\RoleController');*/

// stores
Route::get('add_stores', function () { return view('add_stores/add_stores'); });
Route::post('OrgStores.postdata','OrgStoresController@postdata');
Route::get('OrgStores.loaddata','OrgStoresController@loaddata');
Route::get('OrgStores.edit','OrgStoresController@edit');
Route::get('OrgStores.delete','OrgStoresController@delete');
Route::get('OrgStores.check_Store_Name','OrgStoresController@check_Store_Name');
Route::get('OrgStores.load_fac_locations','OrgStoresController@load_fac_locations');
Route::get('OrgStores.load_fac_section','OrgStoresController@load_fac_section');



/*Route::get('admin/permission/checkName', 'Admin\\PermissionController@checkName');
Route::post('admin/permission/getList', 'Admin\\PermissionController@getList');
Route::post('admin/permission/{id}', 'Admin\\PermissionController@update');
Route::delete('admin/permission/{id}', 'Admin\\PermissionController@destroy');
Route::resource('admin/permission', 'Admin\\PermissionController');*/

/*Route::get('admin/role/checkName', 'Admin\\RoleController@checkName');
Route::post('admin/role/getList', 'Admin\\RoleController@getList');
Route::post('admin/role/{id}', 'Admin\\RoleController@update');
Route::delete('admin/role/{id}', 'Admin\\RoleController@destroy');
Route::resource('admin/role', 'Admin\\RoleController');*/



// Stores module
Route::get('add_stores', function () { return view('add_stores/add_stores'); });
Route::post('OrgStores.postdata','OrgStoresController@postdata');
Route::get('OrgStores.loaddata','OrgStoresController@loaddata');
Route::get('OrgStores.edit','OrgStoresController@edit');
Route::get('OrgStores.delete','OrgStoresController@delete');
Route::get('OrgStores.check_Store_Name','OrgStoresController@check_Store_Name');
Route::get('OrgStores.load_fac_locations','OrgStoresController@load_fac_locations');
Route::get('OrgStores.load_fac_section','OrgStoresController@load_fac_section');

//Cancellation category module
Route::get('add_category','Org\Cancellation\CancellationCategoryController@index');
Route::get('check_category_code','Org\Cancellation\CancellationCategoryController@checkCode');
Route::get('get_all_category','Org\Cancellation\CancellationCategoryController@loadData');
Route::post('save_category','Org\Cancellation\CancellationCategoryController@saveCategory');
Route::get('edit_category','Org\Cancellation\CancellationCategoryController@edit');
Route::get('delete_category','Org\Cancellation\CancellationCategoryController@delete');

//Cancellation Reason module
/*Route::get('add_reason','Org\Cancellation\CancellationReasonController@index');
Route::get('check_reason_code','Org\Cancellation\CancellationReasonController@checkCode');
Route::get('get_all_reason','Org\Cancellation\CancellationReasonController@loadData');
Route::post('save_reason','Org\Cancellation\CancellationReasonController@saveReason');
Route::get('edit_reason','Org\Cancellation\CancellationReasonController@edit');
Route::get('delete_reason','Org\Cancellation\CancellationReasonController@delete');*/

//origin type routes
/*Route::prefix('org/origin-type/')->group(function(){
  //Route::get('origin-type-new','Org\OriginTypeController@new');
  Route::get('check-code','Org\OriginTypeController@check_code');
  Route::post('save','Org\OriginTypeController@save');
  Route::get('list','Org\OriginTypeController@get_list');
  Route::get('get','Org\OriginTypeController@get');
  Route::get('change-status','Org\OriginTypeController@change_status');
});*/


//style creation
//Route::get('loadPart','Merchandising\StyleCreationController@getList');

//Route::resource('customesizes', 'customesizesController');
Route::get('customesizes', 'customesizesController@index');
Route::get('customesizes/getdivision','customesizesController@GetDivisionsByCustomer');
Route::post('customesizes/save_sizes','customesizesController@SaveSizes');
Route::get('customesizes/list_customesizes','customesizesController@LoadCustomeSizes');
Route::get('customesizes/edit_customesizes','customesizesController@EditCustomeSizes');
Route::get('customesizes/delete_customesizes','customesizesController@DeleteCustomeSizes');

//cost center routes sub-category-check-code
Route::get('item','Finance\Item\ItemSubCategoryController@new');
Route::get('sub-category-check-code' , 'Finance\Item\ItemSubCategoryController@check_sub_category_code');
Route::post('sub-category-save','Finance\Item\ItemSubCategoryController@save');
Route::get('sub-category-get-list','Finance\Item\ItemSubCategoryController@get_sub_category_list');
Route::get('sub-category-get','Finance\Item\ItemSubCategoryController@get');
Route::get('sub-category-change-status','Finance\Item\ItemSubCategoryController@change_status');


Route::get('itemproperty', 'itempropertyController@index');
Route::get('get-subcatby-maincat', 'Finance\Item\ItemSubCategoryController@get_subcat_list_by_maincat');
Route::post('itemproperty/save_itemproperty', 'itempropertyController@SaveItemProperty');
Route::get('itemproperty/load-properties', 'itempropertyController@LoadProperties');
Route::post('itemproperty/save-assign', 'itempropertyController@SavePropertyAssign');
//Route::get('itemproperty/load-assign-properties', 'itempropertyController@LoadAssignProperties');
Route::get('itemproperty/load-unassign-bysubcat', 'itempropertyController@LoadUnAssignPropertiesBySubCat');
Route::get('itemproperty/check_property','itempropertyController@CheckProperty');
Route::post('itemproperty/delete-assign', 'itempropertyController@RemoveAssign');





Route::post('itemCreation/saveContent','itemCreationController@SaveContentType');
Route::post('itemCreation/saveComposition','itemCreationController@SaveCompositions');
Route::post('itemCreation/savePropertyValue','itemCreationController@SavePropertyValue');
Route::get('itemCreation/loadContent','itemCreationController@LoadContentType');

Route::get('itemCreation/loadCompositions','itemCreationController@LoadCompositions');
//Route::get('itemCreation/loadPropertyValue','itemCreationController@LoadPropertyValues');
//Route::get('itemCreation/get-maincat', 'itemCreationController@GetMainCategoryByCode');
//Route::post('itemCreation/saveItem','itemCreationController@store');
//Route::get('itemCreation/check-item', 'itemCreationController@CheckItemExist');


Route::prefix('finance/item/')->group(function(){
    Route::get('subcategorylist','Finance\Item\ItemSubCategoryController@LoadSubCategoryList');
    Route::get('maincategorylist','Finance\Item\ItemSubCategoryController@get_category_list');
    Route::post('sub-category-save','Finance\Item\ItemSubCategoryController@save');
    Route::get('get','Finance\Item\ItemSubCategoryController@get');
    Route::get('sub-category-change-status','Finance\Item\ItemSubCategoryController@change_status');
    Route::get('get-subcatby-maincat', 'Finance\Item\ItemSubCategoryController@get_subcat_list_by_maincat');
    Route::get('get-maincat', 'Finance\Item\ItemSubCategoryController@get_subcat_list_by_maincat');
});

Route::post('flashcosting/savecostingdetails', 'Merchandising\Costing\Flash\FlashController@saveCostingDetails');
Route::get('/print-pdf', 'Merchandising\PurchaseOrder@generate_pdf');
/* */
