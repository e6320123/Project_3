<?php

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

Route::get('/',"GameController@index");
Route::get('/{fname}',"GameController@link");

Route::get('/fin/index',"FinController@index");
Route::get('/fin/calc',"FinController@calc");

// Route::get(‘json’, function () {
//     $headers = array(‘Content-Type’ => ‘application/json; charset=utf-8’);
//     $users = DB::table(‘users’)->get();
//     return Response::json($users, 200, $headers, JSON_UNESCAPED_UNICODE);
// });