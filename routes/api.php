<?php

use App\Http\Controllers\Mtg\CardGallaryController;
use App\Http\Controllers\Notion\CardController;
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
Route::resource('/wisdom', App\Http\Controllers\WidsomGuildController::class);
Route::resource('/notion/card', CardController::class);
Route::resource('/mtg', CardGallaryController::class);
