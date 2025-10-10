<?php

use App\Http\Controllers\DB\ExpDBController;
use App\Http\Controllers\Notion\CardController;
use App\Http\Controllers\Notion\ExpansionController;
use App\Http\Controllers\CardJsonFileController;
use App\Http\Controllers\DB\CardInfoDBController;
use App\Http\Controllers\ScryfallController;
use App\Http\Controllers\ShippingLogController;
use App\Http\Controllers\StockpileController;
use App\Http\Controllers\TranslateController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArrivalController;
use App\Http\Controllers\BaseApiController;
use App\Http\Controllers\PromotypeController;
use App\Http\Controllers\VendorTypeController;

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
Route::resource('scryfall', ScryfallController::class);
Route::post('stockpile/import', [StockpileController::class, 
'import']);
Route::post('shipping/import', [ShippingLogController::class, 'import']);
Route::post('shipping/parse', [ShippingLogController::class, 'parse']);
Route::get('arrival/grouping', [ArrivalController::class, 'grouping']);
Route::apiResource('arrival', ArrivalController::class);
Route::apiResource('shipping', ShippingLogController::class);
Route::get('stockpile', [StockpileController::class, 'index']);
Route::get('vendor', [VendorTypeController::class, 'index']);
Route::apiResource('base', BaseApiController::class);
Route::apiResource('promo', PromotypeController::class);
