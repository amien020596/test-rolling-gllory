<?php

use Illuminate\Http\Request;

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

Route::get('/invalid_credential', function () {
    return response()->json(['data' => 'unauthorized']);
})->name('login');

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function () {
    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');
});

Route::group([
    'middleware' => 'auth:api',
    'prefix' => 'gifts'
], function () {
    Route::post('/', 'GiftsController@store');
    Route::put('/', 'GiftsController@update');
    Route::patch('/', 'GiftsController@patch');
    Route::delete('/{id}', 'GiftsController@destroy');
});

Route::group([
    'prefix' => 'gifts'
], function () {
    Route::get('/', 'GiftsController@index');
    Route::get('/{id}', 'GiftsController@show');
});
// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
