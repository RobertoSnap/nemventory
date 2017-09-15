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


//user
//Route::middleware('auth:api')->get('/user/warehouse', function (Request $request) {
//	return $request->user()->warehouses()->get();
//});

Route::middleware( 'auth:api' )->group( function () {


	Route::prefix( '/user' )->group( function () {

		Route::get( '/', function() {
			return Auth::id();
		});
		Route::get( '/name', function() {
			return Auth::user()->name;
		});

		Route::resource( '/warehouse', 'WarehouseController' );
		Route::post( '/warehouse/import', 'WarehouseController@import' );

		Route::get( '/vendor', 'WarehouseController@vendor' );
		Route::post( '/vendor/import', 'WarehouseController@vendorImport' );
		Route::post( '/vendor/create', 'WarehouseController@vendorCreate' );

		Route::get( '/customer', 'WarehouseController@customer' );
		Route::post( '/customer/import', 'WarehouseController@customerImport' );
		Route::post( '/customer/create', 'WarehouseController@customerCreate' );

		Route::resource( '/itemrequest', 'ItemRequestController' );

		Route::resource( '/purchase-order', 'PurchaseOrderController' );
		Route::post( '/purchase-order/{id}/receive', 'PurchaseOrderController@receive' );
		Route::resource( '/sales-order', 'SalesOrderController' );
		Route::post( '/sales-order/{id}/send', 'SalesOrderController@send' );

		Route::prefix( '/stats' )->group( function () {

			Route::get( '/001', function(){
				$stats = array();

				$stats['warehouses'] =  Auth::user()->warehouses()->get();

				$stats['total_xem_balance'] = 0;
				$stats['total_item_count'] = 0;

				foreach ($stats['warehouses'] as $key => $warehouse){
					$accountData = NemSDK::account()->getFromAddress( $warehouse->address );

					$stats['warehouses'][$key]['xem_balance'] = $accountData->account->balance / 1000000;
					$stats['total_xem_balance'] +=  $stats['warehouses'][$key]['xem_balance'];

					$stats['warehouses'][$key]['item_count'] = count(Nemventory::inventory()->getItems($warehouse->address, true));
					$stats['total_item_count'] += $stats['warehouses'][$key]['item_count'];
				}

				return $stats;
			});

		} );

	} );

	Route::prefix( '/warehouse' )->group( function () {

		Route::get( '/', function ( Request $request ) {
			if ( Request( 'byId' ) === "true" ) {
				return \App\Models\Warehouse::all()->keyBy( 'id' );
			}

			return \App\Models\Warehouse::all();

		} );

		Route::get( '/{warehouse}/item', function ( Request $request, $warehouse ) {
			return Nemventory::inventory()->getItems( $warehouse );
		} );

		Route::get( '/info', function ( Request $request ) {
			if ( Request( 'address' ) ) {
				$address = Nemventory::helpers()->addressPlain(Request( 'address' ));
				return Response::json( DB::table('warehouses')->where('address', $address)->first() );
			}
		} );

	} );


	Route::prefix( '/item' )->group( function () {

		Route::get( '/', function ( Request $request ) {
			$items = Nemventory::inventory()->getAllItems();
			foreach ( $items as $key => $item ) {
				$items[ $key ]->label = $item->mosaic->id->name;
			}

			return $items;
		} );

		Route::get( '/{id}', function ( Request $request, $id ) {
			$test = Nemventory::inventory()->getItemDetails( $id );

			return Response::json( $test );
		} );

	} );


	Route::prefix( '/nem' )->group( function () {

		Route::get( '/account/info', function ( Request $request ) {
			$accountData = NemSDK::account()->getFromAddress( $request->address );

			return Response::json( $accountData );
		} );
		Route::get( '/account/balance', function ( Request $request ) {
			$accountData = NemSDK::account()->getFromAddress( $request->address );

			return $accountData->account->balance / 1000000;
		} );

	} );

	Route::prefix( '/fee' )->group( function () {

		Route::get( '/itemrequest', 'FeeController@itemRequest' );

	} );


} );