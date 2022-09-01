<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TimeShowController;
use App\Http\Controllers\AxiosController;
use App\Http\Controllers\WisdomGuildController;
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

// Route::get('/', [WisdomGuildController::class, "index"]);
Route::get('/', function() {return view('axios');});