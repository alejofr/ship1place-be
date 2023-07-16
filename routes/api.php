<?php

use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', 'App\Http\Controllers\Auth\LogoutController@logout');

    // create user sub client
    Route::post('/users/register-sub-client', 'App\Http\Controllers\UserController@storeSubClient');

    //get user
    Route::get('/users/{user}', 'App\Http\Controllers\UserController@show');

    // update user
    Route::put('/users/{user}', 'App\Http\Controllers\UserController@update');

    // delete user
    Route::delete('/users/{user}', 'App\Http\Controllers\UserController@destroy');

    //disable
    Route::put('/users/{user}/disable', 'App\Http\Controllers\UserController@disableUser');

    // check if user belongs to logged user
    Route::get('/users/belongs/{user}', 'App\Http\Controllers\UserController@belongs');

    //customers
    Route::resource('/customers', 'App\Http\Controllers\CustomerController');
    // search customer
    Route::get('/customer/search', 'App\Http\Controllers\CustomerController@search');

    //packages
    Route::resource('/packages', 'App\Http\Controllers\PackageController');

    // search or all packages
    Route::get('/package/search', 'App\Http\Controllers\PackageController@search');

    // request dhl
    Route::post('dhl/rates', 'App\Http\Controllers\DHL\RateController@getRates');
    Route::post('dhl/shipments', 'App\Http\Controllers\DHL\ShipmentController@store');
    Route::post('dhl/pickups', 'App\Http\Controllers\DHL\PickupController@generatePickup');
    Route::delete('dhl/pickups/{pickup}', 'App\Http\Controllers\DHL\PickupController@cancelPickup');
    Route::get('dhl/shipments/{shipment}/get-image', 'App\Http\Controllers\DHL\ShipmentController@getImage');
    Route::get('dhl/tracking/{shipement}', 'App\Http\Controllers\DHL\TrackingController@show');


    // shipements
    Route::resource('/shipments', 'App\Http\Controllers\ControllerShipment');

    // templates
    Route::resource('/templates', 'App\Http\Controllers\TemplateController');

    // users
    Route::post('/users', 'App\Http\Controllers\UserController@index');

    //change password
    Route::post('/user/change-password', 'App\Http\Controllers\UserController@changePassword');

    //current logs
    Route::get('current-logs', 'App\Http\Controllers\LogController@allCurrent');

    // history logs
    Route::get('history-logs', 'App\Http\Controllers\LogController@historyLogs');

    // Tracking DHL
    Route::get('/dhl/tracking/{waybill}', 'App\Http\Controllers\DHL\TrackingController@requestTracking')->name('dhl.tracking');

    // Tracking FEDEX
    Route::get('/fedex/tracking/{waybill}', 'App\Http\Controllers\Fedex\TrackingController@requestTracking')->name('fedex.tracking');

    // Tracking CANADAPOST
    Route::get('/canadapost/tracking/{waybill}', 'App\Http\Controllers\Canadapost\TrackingController@requestTracking')->name('canadapost.tracking');

    // Tracking PUROLATOR
    Route::get('/purolator/tracking/{waybill}', 'App\Http\Controllers\Purolator\TrackingController@requestTracking')->name('purolator.tracking');
});



Route::post('/register', 'App\Http\Controllers\Auth\RegisterController@register');
Route::post('/login', 'App\Http\Controllers\Auth\LoginController@login');
Route::post('/user/recover-password', 'App\Http\Controllers\UserController@recoverPassword');

//countries
Route::resource('/countries', 'App\Http\Controllers\CountryController');
//provinces
Route::resource('/provinces', 'App\Http\Controllers\ProvinceController');
//cities
Route::resource('/cities', 'App\Http\Controllers\CityController');

// search user
Route::post('/user/search', 'App\Http\Controllers\UserController@search');
// get all clients
Route::get('/user/all-clients', 'App\Http\Controllers\UserController@getAllClients');
// search country
Route::get('/country/search', 'App\Http\Controllers\CountryController@search');
// search province
Route::get('/province/search', 'App\Http\Controllers\ProvinceController@search');
// search city
Route::get('/city/search', 'App\Http\Controllers\CityController@search');

// search city
Route::get('/city/search', 'App\Http\Controllers\CityController@search');
