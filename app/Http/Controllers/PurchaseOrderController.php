<?php

namespace App\Http\Controllers;

use App\Events\WarehouseMovement;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderLine;
use App\Models\Warehouse;
use DB;
use Illuminate\Http\Request;
use Nemventory;

class PurchaseOrderController extends Controller {
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index( Request $request ) {
		$purchaseOrders = $request->user()->purchaseOrders();

		return $purchaseOrders;
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create( $warehouse, $vendor, $status, $lines, $attr = array() ) {

		$defaults = [
			'comment' => null
		];
		extract( array_merge( $defaults, $attr ) );

		$po = new PurchaseOrder();

		$po->customer_warehouse_id = $warehouse;
		$po->vendor_warehouse_id   = $vendor;
		$po->comment               = ( isset( $comment ) ) ? $comment : null;
		$po->status                = $status;

		$po->save();

		$poLines = array();
		foreach ( $lines as $line ) {
			array_push( $poLines, new PurchaseOrderLine( [
				'purchase_order_id' => $po->id,
				'comment'           => $line['comment'],
				'item_name'         => $line['item']['mosaic']['id']['name'],
				'item_namespace'    => $line['item']['mosaic']['id']['namespaceId'],
				'quantity'          => $line['quantity'],
				'status'            => 'created'
			] ) );
		}

		$po->lines()->saveMany( $poLines );

		broadcast( new WarehouseMovement( $warehouse, "Purchase order created on one of your warehouses.", "Purchase order created" ) );

		return $po;

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
			'vendor'                             => 'required',
			'vendor.address'                     => array(
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

		$customer_warehouse = \Auth::user()->warehouses()->find( request( 'warehouse.id' ) );

		$vendor_warehouse = DB::table( 'warehouses' )->where( 'address', Nemventory::helpers()->addressPlain( request( 'vendor.address' ) ) )->first();

		if ( ! $vendor_warehouse || ! $customer_warehouse ) {
			abort( '500' );
		}


		$po = $this->create( $customer_warehouse->id, $vendor_warehouse->id, 'created', request( 'lines' ), [
			'comment' => request( 'comment' ),
		] );

		//Create a connected Sales Order
		$so = ( new SalesOrderController() )->create( intval( $customer_warehouse->id ), intval( $vendor_warehouse->id ), 'requested', request( 'lines' ), array(
			'commment' => 'Create from purchase order: ' . $po->id
		) );

		return \Response::json( $po, 200 );
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  \App\PurchaseOrder $purchaseOrder
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function show( Request $request, $id ) {
		$purchaseOrder          = $request->user()->purchaseOrders( $id );
		$purchaseOrder['lines'] = $purchaseOrder->lines()->get();

		return $purchaseOrder;
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  \App\PurchaseOrder $purchaseOrder
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function edit( Request $request, PurchaseOrder $purchaseOrder ) {


	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  \App\PurchaseOrder       $purchaseOrder
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function update( Request $request, PurchaseOrder $purchaseOrder ) {
		$this->validate( request(), [
			'comment' => 'string|nullable',
			'id'      => 'int',
		] );

		$purchaseOrder->update( [
			'comment' => request( 'comment' ),
		] );

		broadcast( new WarehouseMovement( $purchaseOrder->vendor_warehouse_id, "Purchase order " . request( 'id' ) . " was updated.", "Purchase order updated", "success", true ) );


		return request( 'id' );
	}

	public function receive( Request $request, $id ) {


		$purchaseOrder = PurchaseOrder::find( $id );

		if ( ! \Auth::user()->ownWarehouse( $purchaseOrder->customer_warehouse_id ) ) {
			abort( 403, "User dont have permission to receive purchase order." );
		}

		$warehouse          = \Auth::user()->warehouses()->find( $purchaseOrder->customer_warehouse_id );
		$vendor             = Warehouse::find( $purchaseOrder->vendor_warehouse_id );
		$purchaseOrderLines = $purchaseOrder->lines()->get();

		$mosaics = array();
		foreach ( $purchaseOrderLines as $purchaseOrderLine ) {
			$mosaics[] = [
				'namespace' => $purchaseOrderLine->item_namespace,
				'mosaic'    => $purchaseOrderLine->item_name,
				'quantity'  => $purchaseOrderLine->quantity,
			];
		}

		$purchaseOrder->update( [
			'status' => 'received',
		] );

		broadcast( new WarehouseMovement( $purchaseOrder->vendor_warehouse_id, "A purchase order from you have been marked as received.", "Purchase order received." ) );


		broadcast( new WarehouseMovement( $purchaseOrder->customer_warehouse_id, "BETA, not implemented functionality.", "Purchase order received." ) );

		return $purchaseOrder;
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \App\PurchaseOrder $purchaseOrder
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function destroy( PurchaseOrder $purchaseOrder ) {
		//
	}
}
