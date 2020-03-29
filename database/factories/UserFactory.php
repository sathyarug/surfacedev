<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\Models\Org\Color::class, function (Faker $faker) {
    return [
        //'color_id' => rand(0,100),
        'color_name' => $faker->colorName,
        'color_code' => $faker->realText(rand(10,15)),
        //'created_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'created_by'=>rand(0,10),
        //'updated_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'updated_by'=>rand(0,10),
        'status'=>rand(0,1),
    ];
});



$factory->define(App\Models\Org\Customer::class, function (Faker $faker) {
    return [
        //'color_id' => rand(0,100),
        'customer_code' => $faker->numberBetween($min = 1000, $max = 9000),
        'customer_name' => $faker->company,
        //'created_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'customer_short_name'=> $faker->word,
        //'updated_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'type_of_service'=>$faker->numberBetween($min = 0, $max = 8),
        'business_reg_no'=>$faker->swiftBicNumber,
        'business_reg_date'=>$faker->date($format = 'Y-m-d', $max = 'now'),
        'customer_address1'=>$faker->address,
        'customer_address2'=>$faker->address,
        'customer_city'=>$faker->city,
        'customer_postal_code'=>$faker->postcode,
        'customer_state'=>$faker->state,
        'customer_country'=>$faker->country,
        'customer_contact1'=>$faker->phoneNumber,
        'customer_contact2'=>$faker->phoneNumber,
        'customer_contact3'=>$faker->phoneNumber,
        'customer_email'=>$faker->email,
        'customer_map_location'=>$faker->timezone,
        'customer_website'=>$faker->domainName,
        'company_code'=>$faker->userName,
        'operation_start_date'=>$faker->date($format = 'Y-m-d', $max = 'now'),
        'order_destination'=>$faker->country,
        'currency'=>$faker->numberBetween($min = 0, $max = 5),
        'currency'=>$faker->numberBetween($min = 0, $max = 5),
        'boi_reg_no'=>$faker->swiftBicNumber,
        'boi_reg_date'=>$faker->date($format = 'Y-m-d', $max = 'now'),
        'vat_reg_no'=>$faker->swiftBicNumber,
        'svat_no'=>$faker->swiftBicNumber,
        'managing_director_name'=>$faker->name,
        'managing_director_email'=>$faker->email,
        'finance_director_name'=>$faker->name,
        'finance_director_email'=>$faker->email,
        'finance_director_contact'=>$faker->phoneNumber,
        'additional_comments'=>$faker->realText($maxNbChars = 200, $indexSize = 2),
        'ship_terms_agreed'=>$faker->numberBetween($min = 0, $max = 20),
        'payemnt_terms'=>$faker->numberBetween($min = 0, $max = 20),
        'payment_mode'=>$faker->numberBetween($min = 0, $max = 20),
        'bank_acc_no'=>$faker->creditCardNumber,
        'bank_name'=>$faker->domainWord,
        'bank_branch'=>$faker->city,
        'bank_code'=>$faker->citySuffix,
        'bank_swift'=>$faker->swiftBicNumber,
        'bank_iban'=>$faker->swiftBicNumber,
        'bank_contact'=>$faker->phoneNumber,
        'intermediary_bank_name'=>$faker->domainWord,
        'intermediary_bank_address'=>$faker->address,
        'intermediary_bank_contact'=>$faker->phoneNumber,
        'buyer_posting_group'=>$faker->citySuffix,
        'business_posting_group'=>$faker->citySuffix,
        'approved_by'=>$faker->firstNameMale,
        'system_updated_by'=>$faker->firstNameMale ,
        'customer_creation_form'=>$faker->firstNameMale ,
        'updated_by'=>rand(0,10),
        'status'=>rand(0,1),
    ];
});


$factory->define(App\Models\Org\Location\Company::class, function (Faker $faker) {
    return [
        //'color_id' => rand(0,100),
        'company_code' => $faker->word,
        'group_id' => $faker->numberBetween($min = 0, $max = 20),
        //'created_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'company_name'=>$faker->company,
        //'company_name'=>$faker->company,
        'company_address_1'=>$faker->address,
        'company_address_2'=>$faker->address,
        'city'=>$faker->city,
        'country_code'=>$faker->word,
        'company_fax'=>$faker->phoneNumber,
        'company_contact_1'=>$faker->phoneNumber,
        'company_contact_2'=>$faker->phoneNumber,
        'company_logo'=>$faker->word,
        'company_email'=>$faker->email,
        'company_web'=>$faker->domainName,
        'default_currency'=>$faker->word,
        'default_currency'=>$faker->word,
        'finance_month'=>$faker->monthName($max = 'now'),
        'default_currency'=>$faker->word,
        'company_remarks'=>$faker->word,
        'vat_reg_no'=>$faker->word,
        'tax_code'=>$faker->word,
        'company_reg_no'=>$faker->swiftBicNumber,
        'created_by'=>rand(0,10),
        //'updated_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'updated_by'=>rand(0,10),
        'status'=>rand(0,1),
    ];
});

$factory->define(App\Models\Org\Location\Cluster::class, function (Faker $faker) {
    return [
        //'color_id' => rand(0,100),
        'group_code' => $faker->word,
        //'created_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'source_id'=>$faker->numberBetween($min = 0, $max = 20),
        'group_name'=>$faker->word,
        'created_by'=>rand(0,10),
        //'updated_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'updated_by'=>rand(0,10),
        'status'=>rand(0,1),
    ];
});


$factory->define(App\Models\Org\Location\Location::class, function (Faker $faker) {
    return [
        //'color_id' => rand(0,100),
        'loc_code' => $faker->word,
        //'created_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'company_id'=>$faker->numberBetween($min = 0, $max = 20),
        'loc_name'=>$faker->word,
        'loc_type'=>$faker->word,
        'loc_address_1'=>$faker->address,
        'loc_address_2'=>$faker->address,
        'city'=>$faker->city,
        'postal_code'=>$faker->postcode,
        'loc_phone'=>$faker->phoneNumber,
        'loc_fax'=>$faker->phoneNumber,
        'loc_email'=>$faker->email,
        'loc_web'=>$faker->domainName,
        'time_zone'=>$faker->timezone,
        'currency_code'=>$faker->word,
        'loc_google'=>$faker->latitude($min = -90, $max = 90),
        'currency_code'=>$faker->word,
        'state_Territory'=>$faker->state,
        'type_of_loc'=>$faker->numberBetween($min = 0, $max = 20),
        'country_code'=>$faker->word,
        'land_acres'=>$faker->word,
        'type_property'=>$faker->numberBetween($min = 0, $max = 20),
        'type_property'=>$faker->numberBetween($min = 0, $max = 20),
        'fix_asset'=>$faker->numberBetween($min = 0, $max = 20),
        'opr_start_date'=>$faker->dateTime($max = 'now', $timezone = null),
        'latitude'=>$faker->latitude($min = -90, $max = 90),
        'longitude'=>$faker->longitude($min = -180, $max = 180) ,
        'created_by'=>rand(0,10),

        //'updated_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'updated_by'=>rand(0,10),
        'status'=>rand(0,1),
    ];
});


$factory->define(App\Models\Org\Location\Source::class, function (Faker $faker) {
    return [
        //'color_id' => rand(0,100),
        'source_code' => $faker->word,
        'source_name' => $faker->realText(rand(10,15)),
        //'created_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'created_by'=>rand(0,10),
        //'updated_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'updated_by'=>rand(0,10),
        'status'=>rand(0,1),
    ];
});



$factory->define(App\Models\Org\Department::class, function (Faker $faker) {
    return [
        //'color_id' => rand(0,100),
        'dep_code' => $faker->numerify('Dep###'),
        'dep_name' => $faker->lexify('Department ???'),

        //'created_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'created_by'=>rand(0,10),
        //'updated_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'updated_by'=>rand(0,10),
        'status'=>rand(0,1),
    ];
});



$factory->define(App\Models\Org\Section::class, function (Faker $faker) {
    return [
        //'color_id' => rand(0,100),
        'section_code' => $faker->numerify('Sec###'),
        'section_name' => $faker->lexify('Section ???'),

        //'created_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'created_by'=>rand(0,10),
        //'updated_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'updated_by'=>rand(0,10),
        'status'=>rand(0,1),
    ];
});



$factory->define(App\Models\Org\ProductSpecification::class, function (Faker $faker) {
    return [
        //'color_id' => rand(0,100),
        'prod_cat_description' => $faker->lexify('Description ???'),
        //'created_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'created_by'=>rand(0,10),
        //'updated_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'updated_by'=>rand(0,10),
        'status'=>rand(0,1),
    ];
});


$factory->define(App\Models\Org\Division::class, function (Faker $faker) {
    return [
        //'color_id' => rand(0,100),
        'division_code' => $faker->numerify('Div###'),
        'division_description' => $faker->lexify('Division discription ???'),
        //'created_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'created_by'=>rand(0,10),
        //'updated_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'updated_by'=>rand(0,10),
        'status'=>rand(0,1),
    ];
});


$factory->define(App\Models\Org\Size::class, function (Faker $faker) {
    return [
        //'color_id' => rand(0,100),
        'division_id' => $faker->numberBetween($min = 0, $max = 20),
        'size_name' => $faker->lexify('size ???'),
        //'created_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'created_by'=>rand(0,10),
        //'updated_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'updated_by'=>rand(0,10),
        'status'=>rand(0,1),
    ];
});



$factory->define(App\Models\Merchandising\ColorOption::class, function (Faker $faker) {
    return [

        'color_option' => $faker->lexify('color option ???'),
        //'created_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'created_by'=>rand(0,10),
        //'updated_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'updated_by'=>rand(0,10),
        'status'=>rand(0,1),
    ];
});


$factory->define(App\Models\Org\UOM::class, function (Faker $faker) {
    return [

        'uom_code' => $faker->numerify('UOM###'),
        'uom_description'=> $faker->lexify('description test ???'),
        //'created_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'uom_factor'=> $faker->lexify('test factor ???'),
        'uom_base_unit'=>$faker->lexify('test base unit ???'),
        'unit_type'=> $faker->lexify('test unit type ???'),
        'created_by'=>rand(0,10),
        //'updated_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'updated_by'=>rand(0,10),
        'status'=>rand(0,1),
    ];
});


$factory->define(App\Models\Org\Cancellation\CancellationCategory::class, function (Faker $faker) {
    return [

        'category_code' => $faker->numerify('category_code###'),
        'category_description'=> $faker->lexify('description test ???'),
        //'created_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'created_by'=>rand(0,10),
        //'updated_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'updated_by'=>rand(0,10),
        'status'=>rand(0,1),
    ];
});



$factory->define(App\Models\Org\Cancellation\CancellationReason::class, function (Faker $faker) {
    return [

        'reason_code' => $faker->numerify('category_code###'),
        'reason_description'=> $faker->lexify('description test ???'),
        //'created_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'reason_category'=> $faker->numberBetween($min = 0, $max = 20),
        'created_by'=>rand(0,10),
        //'updated_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'updated_by'=>rand(0,10),
        'status'=>rand(0,1),
    ];
});


$factory->define(App\Models\Org\Supplier::class, function (Faker $faker) {
    return [

        'supplier_code' => $faker->numerify('sup###'),
        'supplier_name'=>$faker->company,
        //'created_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'supplier_short_name'=> $faker->userName,
        'type_of_service'=>$faker->numberBetween($min = 0, $max = 20),
        'business_reg_no' => $faker->numerify('reg###'),
        //'business_reg_date'
        'supplier_address1'=>$faker->address,
        'supplier_address2'=>$faker->address,
        'supplier_city'=>$faker->city,
        'supplier_postal_code'=>$faker->postcode,
        'supplier_state'=>$faker->state,
        'supplier_country'=>$faker->country,
        'supplier_contact1'=>$faker->phoneNumber,
        'supplier_contact2'=>$faker->phoneNumber,
        'supplier_contact3'=>$faker->phoneNumber,
        'supplier_email'=>$faker->email,
        'supplier_map_location'=>$faker->latitude($min = -90, $max = 90),
        'supplier_website'=>$faker->domainName,
        'company_code'=>$faker->numerify('comp###'),
        //'operation_start_date'$faker->numerify('comp###'),
        'order_destination'=>$faker->city,
        'currency'=>$faker->numberBetween($min = 0, $max = 20),
        'boi_reg_no'=>$faker->numerify('boi_reg###'),
        'order_destination'=>$faker->city,
        //'boi_reg_date'
        'vat_reg_no'=>$faker->numerify('vat_reg###'),
        'svat_no'=>$faker->numerify('svat_reg###'),
        'managing_director_name'=>$faker->name,
        'managing_director_email'=>$faker->email,
        'finance_director_name'=>$faker->name,
        'finance_director_email'=>$faker->email,
        'finance_director_contact'=>$faker->phoneNumber,
        'additional_comments'=>$faker->realText($maxNbChars = 200, $indexSize = 2),
        'ship_terms_agreed'=>$faker->numberBetween($min = 0, $max = 1),
        'additional_comments'=>$faker->realText($maxNbChars = 200, $indexSize = 2),
        'payemnt_terms'=>$faker->numberBetween($min = 0, $max = 20),
        'payment_mode'=>$faker->numberBetween($min = 0, $max = 20),
        'bank_acc_no'=>$faker->creditCardNumber,
        'bank_name'=>$faker->domainWord,
        'bank_branch' =>$faker->city,
        'bank_code'=>$faker->numerify('bank_code###'),
        'bank_swift' =>$faker->swiftBicNumber,
        'bank_iban' =>$faker->swiftBicNumber,
        'intermediary_bank_name'=>$faker->domainWord,
        'intermediary_bank_address'=>$faker->address,
        'intermediary_bank_contact'=>$faker->phoneNumber,
        'buyer_posting_group'=>$faker->citySuffix,
        'business_posting_group'=>$faker->citySuffix,
        'approved_by'=>$faker->firstNameMale,
        'system_updated_by'=>$faker->firstNameMale ,
        'customer_creation_form'=>$faker->firstNameMale ,
        'created_by'=>rand(0,10),
        //'updated_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'updated_by'=>rand(0,10),
        'status'=>rand(0,1),
    ];
});


$factory->define(App\Models\Org\Country::class, function (Faker $faker) {
    return [

        'country_code' => $faker->numerify('country###'),
        'country_description'=> $faker->numerify('country###'),
        //'created_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'created_by'=>rand(0,10),
        //'updated_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'updated_by'=>rand(0,10),
        'status'=>rand(0,1),
    ];
});



$factory->define(App\Models\Finance\Accounting\PaymentTerm::class, function (Faker $faker) {
    return [

        'payment_code'  => $faker->numerify('payment###'),
        'payment_description'=> $faker->numerify('Test payemnt Description###'),
        //'created_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'created_by'=>rand(0,10),
        //'updated_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'updated_by'=>rand(0,10),
        'status'=>rand(0,1),
    ];
});




$factory->define(App\Models\Finance\Accounting\PaymentMethod::class, function (Faker $faker) {
    return [

        'payment_method_code'  => $faker->numerify('paymentMethod###'),
        'payment_method_description'=> $faker->numerify('Test payemnt Method Description###'),
        //'created_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'created_by'=>rand(0,10),
        //'updated_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'updated_by'=>rand(0,10),
        'status'=>rand(0,1),
    ];
});



$factory->define(App\Models\Finance\Accounting\CostCenter::class, function (Faker $faker) {
    return [

        'cost_center_code'  => $faker->numerify('center###'),
        'loc_id'=>$faker->numberBetween($min = 0, $max = 20),
        //'created_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'cost_center_name' => $faker->numerify('center name###'),
        'created_by'=>rand(0,10),
        //'updated_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'updated_by'=>rand(0,10),
        'status'=>rand(0,1),
    ];
});

$factory->define(App\Models\Finance\Currency::class, function (Faker $faker) {
    return [

        'currency_code'  => $faker->currencyCode,
        'currency_description'=> $faker->numerify('Test currency Description###'),
        //'created_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'created_by'=>rand(0,10),
        //'updated_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'updated_by'=>rand(0,10),
        'status'=>rand(0,1),
    ];
});



$factory->define(App\Models\Org\OriginType::class, function (Faker $faker) {
    return [

        'origin_type'  => $faker->numerify('Test origin Type###'),
      //'created_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'created_by'=>rand(0,10),
        //'updated_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'updated_by'=>rand(0,10),
        'status'=>rand(0,1),
    ];
});

$factory->define(App\Models\Finance\GoodsType::class, function (Faker $faker) {
    return [

        'goods_type_description'  => $faker->numerify('Test goods discription###'),
      //'created_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'created_by'=>rand(0,10),
        //'updated_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'updated_by'=>rand(0,10),
        'status'=>rand(0,1),
    ];
});

$factory->define(App\Models\Finance\ShipmentTerm::class, function (Faker $faker) {
    return [

        'ship_term_code'  => $faker->numerify('TestCode###'),
      //'created_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'ship_term_description'=> $faker->numerify('test Ship term discription###'),
        'created_by'=>rand(0,10),
        //'updated_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'updated_by'=>rand(0,10),
        'status'=>rand(0,1),
    ];
});


$factory->define(App\Models\Finance\Transaction::class, function (Faker $faker) {
    return [

        'trans_code'  => $faker->numerify('testCode###'),
        'trans_description'=> $faker->numerify('test trance description###'),
      //'created_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'created_by'=>rand(0,10),
        //'updated_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'updated_by'=>rand(0,10),
        'status'=>rand(0,1),
    ];
});

$factory->define(App\Models\Finance\ExchangeRate::class, function (Faker $faker) {
    return [

        'currency'  => $faker->numberBetween($min = 1, $max = 20),
        'rate'=> $faker->numberBetween($min = 1, $max = 20),
        //'valid_from'
        //'created_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'created_by'=>rand(0,10),
        //'updated_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'updated_by'=>rand(0,10),
        'status'=>rand(0,1),
    ];
});

$factory->define(App\Models\Org\SupplierTolarance::class, function (Faker $faker) {
    return [

        'supplier_id'  => $faker->numberBetween($min = 1, $max = 10),
        'category_id'  => $faker->numberBetween($min = 1, $max = 10),
        'subcategory_id'=> $faker->numberBetween($min = 1, $max = 10),
        'subcategory_id'=> $faker->numberBetween($min = 1, $max = 10),
        'uom_id'=> $faker->numberBetween($min = 1, $max = 10),
        'uom_id'=> $faker->numberBetween($min = 1, $max = 10),
        'qty'=> $faker->numberBetween($min = 1000, $max = 1500),
        'min'=> $faker->numberBetween($min = 1000, $max = 1500),
        'max'=> $faker->numberBetween($min = 1000, $max = 1500),
        'min_qty'=> $faker->numberBetween($min = 1000, $max = 1500),
        'max_qty'=> $faker->numberBetween($min = 1000, $max = 1500),
        //'valid_from'
        //'created_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'created_by'=>rand(0,10),
        //'updated_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'updated_by'=>rand(0,10),
        'status'=>rand(0,1),
    ];
});



$factory->define(App\itemCreation::class, function (Faker $faker) {
    return [

        'subcategory_id'  => $faker->numberBetween($min = 1, $max = 10),
        'master_code'  => $faker->numerify('testMasterCode###'),
        'master_description'=> $faker->numerify('test description###'),
        'uom_id'=> $faker->numberBetween($min = 1, $max = 10),
        //'valid_from'
        //'created_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'created_by'=>rand(0,10),
        //'updated_date'=>$faker=>dateTime($max = 'now', $timezone = null),
        'updated_by'=>rand(0,10),
        'status'=>rand(0,1),
    ];
});
