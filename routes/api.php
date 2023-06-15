<?php

use App\Http\Controllers\DB\ExpDBController;
use App\Http\Controllers\Notion\CardController;
use App\Http\Controllers\Notion\ExpansionController;
use App\Http\Controllers\CardJsonFileController;
use App\Http\Controllers\DB\CardInfoDBController;
use App\Http\Controllers\ShippingLogController;
use App\Http\Controllers\StockpileController;
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
Route::resource('/notion/card', CardController::class);
Route::resource('notion/expansion', ExpansionController::class);
Route::resource('database/exp', ExpDBController::class);
Route::resource('database/card', CardInfoDBController::class);
Route::post('upload/card', [CardJsonFileController::class, 'uploadCardFile']);
Route::post('stockpile/import', [StockpileController::class, 'import']);
Route::post('shipping/import', [ShippingLogController::class, 'import']);