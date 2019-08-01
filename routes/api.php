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

Route::post('register', 'AuthController@register');
Route::post('login', 'AuthController@login');
Route::post('recover', 'AuthController@recover');
Route::group(['middleware' => ['jwt.auth']], function() {
    Route::get('/notifications', 'NotificationController@index');
    Route::get('/users/current', 'UserController@getCurrent');
    Route::post('/notifications/read/{id}', 'NotificationController@read');
    Route::get('logout', 'AuthController@logout'); 
   
    Route::get('sarpras', 'SarprasController@index');
    Route::post('sarpras', 'SarprasController@store');
    Route::delete('sarpras/{id}', 'SarprasController@destroy');
    Route::post('sarpras/{id}', 'SarprasController@update');
    Route::get('sarpras/{id}', 'SarprasController@show');
    Route::get('sarpras/search/{id}', 'SarprasController@searchByLokasi');

    Route::post('lokasi', 'LokasiController@store');
    Route::get('lokasi', 'LokasiController@index');
    Route::get('gedung', 'LokasiController@getGedung');
    Route::delete('lokasi/{id}', 'LokasiController@destroy');
    Route::post('lokasi/{id}', 'LokasiController@update');
    Route::get('lokasi/search/{name}', 'LokasiController@searchByName');    

    Route::post('units', 'UnitController@store');
    Route::get('units', 'UnitController@index');
    Route::delete('units/{id}', 'UnitController@destroy');
    Route::post('units/{id}', 'UnitController@update');

    Route::get('komplains', 'KomplainController@index');
    Route::post('komplains', 'KomplainController@store');
    Route::delete('komplains/{id}', 'KomplainController@destroy');
    Route::post('komplains/{id}', 'KomplainController@update');
    Route::get('komplains/{id}', 'KomplainController@show');

    Route::post('komplain/tindak/{id}', 'KomplainController@tindak');
    Route::post('komplain/laporan/{id}', 'KomplainController@laporan');
    Route::post('komplain/disposisi/{id}', 'KomplainController@disposisi');

    Route::get('riwayat/aset', 'RiwayatAsetController@index');
    Route::get('riwayat/perintah', 'RiwayatPerintahController@index');

    Route::get('perintahs', 'PerintahController@index');
    Route::post('perintahs', 'PerintahController@store');
    Route::delete('perintahs/{id}', 'PerintahController@destroy');
    Route::post('perintahs/{id}', 'PerintahController@update');
    Route::get('perintahs/{id}', 'PerintahController@show');

    Route::post('perintah/tindak/{id}', 'PerintahController@tindak');
    Route::post('perintah/laporan/{id}', 'PerintahController@laporan');

    Route::get('kondisi', 'SarprasController@getKondisi');
    Route::get('status', 'SarprasController@getStatus');

    Route::get('/users', 'UserController@getUsers');
    Route::post('/users/{id}', 'UserController@update');
    Route::get('/users/role/{id}/{index}', 'UserController@setRole');
    Route::get('/users/status/{id}', 'UserController@setStatus');
    Route::get('users/{id}', 'UserController@show');
    Route::delete('users/{id}', 'UserController@destroy');
    
    Route::get('items', 'ItemController@index');  
    Route::get('items/search/{name}', 'ItemController@searchByName');    
    Route::get('items/array', 'ItemController@array_data');
    Route::post('items', 'ItemController@store');
    Route::delete('items/{id}', 'ItemController@destroy');
    Route::put('items/{id}', 'ItemController@update');
    Route::get('items/{id}', 'ItemController@show');
    
    Route::get('products', 'ProductController@index');
     Route::get('products/search/{name}', 'ProductController@searchByName');
    Route::get('products/array', 'ProductController@array_data');
    Route::post('products', 'ProductController@store');
    Route::delete('products/{id}', 'ProductController@destroy');
    Route::post('products/{id}', 'ProductController@update');
    Route::get('products/{id}', 'ProductController@show');

    Route::get('product-items', 'ProductItemController@index');
    Route::post('product-items', 'ProductItemController@store');
    Route::delete('product-items/{id}', 'ProductItemController@destroy');
    Route::put('product-items/{id}', 'ProductItemController@update');
    Route::get('product-items/{id}', 'ProductItemController@show');

    Route::get('transactions', 'TransactionController@index');
    Route::post('transactions', 'TransactionController@store');
    Route::delete('transactions/{id}', 'TransactionController@destroy');
    Route::put('transactions/{id}', 'TransactionController@update');
    Route::get('transactions/{id}', 'TransactionController@show');

    Route::get('transaction-details/transaction/{transaction_id}', 'TransactionDetailController@showByTransaction');
    Route::delete('transaction-details/{id}', 'TransactionDetailController@destroy');
});
