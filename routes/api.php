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

// Auth
Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function () {
    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');
    Route::post('profile', 'AuthController@profile');
});

Route::group([
    'middleware' => 'auth:api',
    'prefix' => 'gifts'
], function () {
    Route::post('/', 'GiftsController@store');
    Route::put('/{id}', 'GiftsController@update_put');
    Route::patch('/{id}', 'GiftsController@update_patch');
    Route::delete('/{id}', 'GiftsController@destroy');
});

Route::group([
    'middleware' => 'auth:api',
    'prefix' => 'roles'
], function () {
    Route::post('/', 'RBACContoller@create_role');
    Route::post('/permission_to_role', 'RBACContoller@assign_permission_to_role');
});

Route::group([
    'middleware' => 'auth:api',
    'prefix' => 'permission'
], function () {
    Route::post('/', 'RBACContoller@create_permission');
});






// Public
Route::group([
    'prefix' => 'gifts'
], function () {
    Route::get('/', 'GiftsController@index');
    Route::get('/{id}', 'GiftsController@show');
});

Route::group([
    'prefix' => 'users'
], function () {
    Route::post('/register', 'UsersController@register');
});
// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
