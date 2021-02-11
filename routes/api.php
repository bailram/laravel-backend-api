<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CEOController;
use App\Http\Controllers\API\DocumentController;
use App\Http\Controllers\API\ImageController;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::apiResource('/ceo', CEOController::class)->middleware('auth:api');

Route::prefix('file')->group(function(){
    Route::post('store', [DocumentController::class, 'store']);
    Route::get('load/{filename}', [DocumentController::class, 'load']);
});

Route::prefix('image')->group(function(){
    Route::get('load/{filename}', [ImageController::class, 'load']);
    Route::post('store', [ImageController::class, 'store']);
});
