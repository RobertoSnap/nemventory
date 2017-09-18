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

Route::get('/', function () {
	return view('welcome');
});

Auth::routes();

Route::get('/web', 'HomeController@index');


Route::get('/test', function ( ) {

	dispatch(new \App\Jobs\ImportTransactions());
	dispatch(new \App\Jobs\JobDispatcher());
	dispatch(new \App\Jobs\FetchNemItems());



});
Route::get('/test2', function ( ) {

	dd( NemSDK::models()->account()->generate());

});
