<?php

use App\Http\Controllers\CertificateController;
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
Route::group(['prefix' => 'certificate'], function() {
    Route::post('/buy',[CertificateController::class,'buy_certificate']);
    Route::post('/activate',[CertificateController::class,'activate_certificate']);
    Route::get('/list',[CertificateController::class,'activated_certificate']);
});
