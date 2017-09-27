<?php

namespace App\Http\Controllers;

use App\Events\WarehouseMovement;
use App\Models\SalesOrder;
use App\Models\SalesOrderLine;
use App\Models\Warehouse;
use DB;
use Illuminate\Http\Request;
use Nemventory;

class SalesOrderController extends Controller {
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index( Request $request ) {
		$so = $request->user()->salesOrders();

		return $so;
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create( $customer, $warehouse, $status, $lines, $attr = array() ) {
		$defaults = [
			'comment' => null
		];
		extract( array_merge( $defaults, $attr ) );
		$so = new SalesOrder();

		$so->customer_warehouse_id = $customer;
		$so->vendor_warehouse_id   = $warehouse;
		$so->comment               = ( isset( $comment ) ) ? $comment : null;
		$so->status                = $status;

		$so->save();

		$soLines = array();
		foreach ( $lines as $line ) {
			array_push( $soLines, new SalesOrderLine( [
				'sales_order_id' => $so->id,
				'comment'        => $line['comment'],
				'item_name'      => $line['item']['mosaic']['id']['name'],
				'item_namespace' => $line['item']['mosaic']['id']['namespaceId'],
				'quantity'       => $line['quantity'],
				'status'         => 'created'
			] ) );
		}

		$so->lines()->saveMany( $soLines );

		broadcast( new WarehouseMovement(
				$so->vendor_warehouse_id,
				"A sales order has been created on  " . request( 'warehouse.name' ) . ".",
				"Sales order created",
				"success" )
		);

		return $so;
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function store( Request $request ) {
		$this->validate( request(), [
			'customer'                           => 'required',
			'customer.address'                   => array(
				'required',
				'regex: /^[a-zA-Z0-9]+$/',
			),
			'warehouse'                          => 'array|required',
			'warehouse.id'                       => 'int|required',
			'comment'                            => 'string|nullable',
			'lines.*.comment'                    => 'string|nullable',
			'lines.*.item'                       => 'array|required',
			'lines.*.item.mosaic.id.name'        => 'string|required',
			'lines.*.item.mosaic.id.namespaceId' => 'string|required',
			'lines.*.quantity'                   => 'integer|required',
		] );

		$vendor_warehouse = \Auth::user()->warehouses()->find( request( 'warehouse.id' ) );

		$customer_warehouse = DB::table( 'warehouses' )->where( 'address', Nemventory::helpers()->addressPlain( request( 'customer.address' ) ) )->first();

		if ( ! $vendor_warehouse || ! $customer_warehouse ) {
			abort( '500' );
		}

		$so = $this->create( intval( $customer_warehouse->id ), $vendor_warehouse->id, 'created', request( 'lines' ), [
			'comment' => request( 'comment' )
		] );

		$po = ( new PurchaseOrderController() )->create( $customer_warehouse->id, $vendor_warehouse->id, 'proposed', request( 'lines' ), [
			'comment' => 'Preposed from sales order: ' . $so->id
		] );


		return \Response::json( $so, 200 );
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  \App\SalesOrder $salesOrder
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function show( Request $request, $id ) {
		$salesOrder          = $request->user()->salesOrders( $id );
		$salesOrder['lines'] = $salesOrder->lines()->get();

		return $salesOrder;
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  \App\SalesOrder $salesOrder
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function edit( SalesOrder $salesOrder ) {
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  \App\SalesOrder          $salesOrder
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function update( Request $request, SalesOrder $salesOrder ) {
		$this->validate( request(), [
			'comment' => 'string|nullable',
			'id'      => 'int',
		] );

		$salesOrder->update( [
			'comment' => request( 'comment' ),
		] );

		broadcast( new WarehouseMovement(
				$salesOrder->vendor_warehouse_id,
				"Sales order " . request( 'id' ) . " was updated.",
				"Sales order updated",
				"success",
				true )
		);


		return request( 'id' );
	}

	public function send( Request $request, $id ) {

		$salesOrder = SalesOrder::find( $id );

		if ( ! \Auth::user()->ownWarehouse( $salesOrder->vendor_warehouse_id ) ) {
			abort( 403, "User dont have permission to send sales order." );
		}

		$warehouse       = \Auth::user()->warehouses()->find( $salesOrder->vendor_warehouse_id );
		$customer        = Warehouse::find( $salesOrder->customer_warehouse_id );
		$salesOrderLines = $salesOrder->lines()->get();

		$mosaics = array();
		foreach ( $salesOrderLines as $salesOrderLine ) {
			$mosaics[] = [
				'namespace' => $salesOrderLine->item_namespace,
				'mosaic'    => $salesOrderLine->item_name,
				'quantity'  => $salesOrderLine->quantity,
			];
		}
		$res = Nemventory::transaction()->transfer( $warehouse, $customer->address, 1, "SalesOrder." . $salesOrder->id, $mosaics );

		if ( property_exists( $res, 'message' ) && $res->message === "SUCCESS" ) {
			$salesOrder->update( [
				'status' => 'sent',
			] );

			return \Response::json( $res, 200 );
		} elseif ( property_exists( $res, 'message' ) ) {
			return \Response::json( array(
				'form' => [
					$res->message
				]
			), 401 );
		} else {
			return \Response::json( array(
				'form' => [
					$res
				]
			), 401 );
		}
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \App\SalesOrder $salesOrder
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function destroy( SalesOrder $salesOrder ) {
		//
	}
}
