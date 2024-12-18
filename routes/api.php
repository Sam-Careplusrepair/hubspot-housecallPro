<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\HubspotHousecallMiddleware2;
use App\Http\Controllers\HubSpotController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::get('/home', [SamController::class, 'index']);

Route::get('/example', function(){
    return response()->json(['message'=>'API Route is working']);
});

Route::post('/sync', function (Request $request) {
    // Your sync logic here (if needed).
})->middleware('hubspot.housecall');

Route::post('/sync-data', function (Request $request) {
    $middleware = new HubspotHousecallMiddleware2();
    return $middleware->handle($request, function () {});
});

Route::get('/hubspot/contacts', [HubSpotcontroller::class, 'getHubSpotContacts']);

// Route::get('/hubspot/customers', function () {
//     $middleware = new HubspotHousecallMiddleware2();
//     return response()->json($middleware->fetchHubSpotRecords());
// });

Route::get('/housecall/customers', function () {
    $middleware = new HubspotHousecallMiddleware2();
    return response()->json($middleware->fetchHousecallRecords());
});

Route::post('/hubspot/customers', function (Request $request){
    $middleware = new HubspotHousecallMiddleware2();
    return $middleware->syncToHubspot($request->all());
});

Route::post('/housecall/customers', function (Request $request){
    $middleware = new HubspotHousecallMiddleware2();
    return $middleware->syncToHousecallPro($request->all());
});
