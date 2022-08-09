<?php

use Illuminate\Support\Facades\Route;

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

//Route::get('/', function () {return view('welcome');});
//Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/', 'App\Http\Controllers\DashboardController@index');
Route::get('dashboard', 'App\Http\Controllers\DashboardController@index');
Route::get('/home', 'App\Http\Controllers\DashboardController@index');
Route::get('logout', 'App\Http\Controllers\DashboardController@logout');

Auth::routes(); // 認証関連

// 売上管理画面
Route::get('sales', 'App\Http\Controllers\SalesController@index');
Route::get('sales/create', 'App\Http\Controllers\SalesController@create');
Route::post('sales/store', 'App\Http\Controllers\SalesController@store');
Route::get('sales/show', 'App\Http\Controllers\SalesController@show');
Route::get('sales/edit', 'App\Http\Controllers\SalesController@edit');
Route::post('sales/update', 'App\Http\Controllers\SalesController@update');
Route::post('sales/auto_save', 'App\Http\Controllers\SalesController@auto_save');
Route::post('sales/disabled', 'App\Http\Controllers\SalesController@disabled');
Route::post('sales/destroy', 'App\Http\Controllers\SalesController@destroy');
Route::get('sales/csv_download', 'App\Http\Controllers\SalesController@csv_download');

// 顧客管理画面
Route::get('client', 'App\Http\Controllers\ClientController@index');
Route::get('client/create', 'App\Http\Controllers\ClientController@create');
Route::post('client/store', 'App\Http\Controllers\ClientController@store');
Route::get('client/show', 'App\Http\Controllers\ClientController@show');
Route::get('client/edit', 'App\Http\Controllers\ClientController@edit');
Route::post('client/update', 'App\Http\Controllers\ClientController@update');
Route::post('client/auto_save', 'App\Http\Controllers\ClientController@auto_save');
Route::post('client/disabled', 'App\Http\Controllers\ClientController@disabled');
Route::post('client/destroy', 'App\Http\Controllers\ClientController@destroy');
Route::get('client/csv_download', 'App\Http\Controllers\ClientController@csv_download');

// ネコ管理画面
Route::get('neko', 'App\Http\Controllers\NekoController@index');
Route::get('neko/create', 'App\Http\Controllers\NekoController@create');
Route::post('neko/store', 'App\Http\Controllers\NekoController@store');
Route::get('neko/show', 'App\Http\Controllers\NekoController@show');
Route::get('neko/edit', 'App\Http\Controllers\NekoController@edit');
Route::post('neko/update', 'App\Http\Controllers\NekoController@update');
Route::post('neko/auto_save', 'App\Http\Controllers\NekoController@auto_save');
Route::post('neko/disabled', 'App\Http\Controllers\NekoController@disabled');
Route::post('neko/destroy', 'App\Http\Controllers\NekoController@destroy');
Route::get('neko/csv_download', 'App\Http\Controllers\NekoController@csv_download');

// ネコ種別管理画面
Route::get('neko_type', 'App\Http\Controllers\ClientController@index');
Route::get('neko_type/create', 'App\Http\Controllers\ClientController@create');
Route::post('neko_type/store', 'App\Http\Controllers\ClientController@store');
Route::get('neko_type/show', 'App\Http\Controllers\ClientController@show');
Route::get('neko_type/edit', 'App\Http\Controllers\ClientController@edit');
Route::post('neko_type/update', 'App\Http\Controllers\ClientController@update');
Route::post('neko_type/auto_save', 'App\Http\Controllers\ClientController@auto_save');
Route::post('neko_type/disabled', 'App\Http\Controllers\ClientController@disabled');
Route::post('neko_type/destroy', 'App\Http\Controllers\ClientController@destroy');
Route::get('neko_type/csv_download', 'App\Http\Controllers\ClientController@csv_download');


// ユーザー管理画面
Route::get('user_mng', 'App\Http\Controllers\UserMngController@index');
Route::get('user_mng/create', 'App\Http\Controllers\UserMngController@create');
Route::post('user_mng/store', 'App\Http\Controllers\UserMngController@store');
Route::get('user_mng/show', 'App\Http\Controllers\UserMngController@show');
Route::get('user_mng/edit', 'App\Http\Controllers\UserMngController@edit');
Route::post('user_mng/update', 'App\Http\Controllers\UserMngController@update');
Route::post('user_mng/auto_save', 'App\Http\Controllers\UserMngController@auto_save');
Route::post('user_mng/disabled', 'App\Http\Controllers\UserMngController@disabled');
Route::post('user_mng/destroy', 'App\Http\Controllers\UserMngController@destroy');
Route::get('user_mng/csv_download', 'App\Http\Controllers\UserMngController@csv_download');

