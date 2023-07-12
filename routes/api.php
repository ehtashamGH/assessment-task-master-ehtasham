<?php

use App\Jobs\PayoutOrderJob;
use App\Services\ApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get("test",function(){
//    $app=new ApiService();
//    $app->sendPayout("clare.graham@example.org",227.00);
    dispatch(new PayoutOrderJob(\App\Models\Order::find(1)));
});


