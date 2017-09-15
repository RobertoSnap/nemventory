<?php

namespace App\Http\Controllers;

use App\Events\WarehouseMovement;
use App\Models\ItemRequest;
use Nemventory;
use Illuminate\Http\Request;
use NemSDK;
use Illuminate\Support\Facades\Response;

class ItemRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     */
    public function store(Request $request)
    {
	    $this->validate( request(), [
		    'name'         => array(
			    'required',
			    'regex:/^[a-z]{1}[a-zA-Z0-9_]{0,31}$/',
		    ),
		    'description'  => 'required',
		    'initialStock' => 'required|max:9000000000|min:0',
		    'divisbility'  => 'required|max:6|min:0',
		    'warehouse'    => array(
			    'required',
			    'regex:/[a-zA-Z0-9]+/',
		    ),
		    'fee'          => 'required|min:0|max:50'
	    ] );

		//Calculate fee
	    $fee = Nemventory::helpers()->toMicroXem(\request( 'fee' ), 'ItemRequest.xxx');
	    $first_transaction_fee = NemSDK::models()->fee()->transfer( $fee, 'ItemRequest.xxx' );
	    $amount_to_transfer    = $fee - $first_transaction_fee;

	    $warehouse = \Auth::user()->warehouses()->where('address', request( 'warehouse' ))->first();

	    if( ! \Auth::user()->ownWarehouse( $warehouse->id) ){
	    	abort(403, "Does not own warehouse");
	    }

	    $item_request = (new ItemRequest)->create( [
		    'name'            => request( 'name' ),
		    'description'     => request( 'description' ),
		    'warehouse_id'    => $warehouse->id,
		    'status'          => 'requested',
		    'initial_stock'   => request( 'initialStock' ),
		    'divisbility'     => request( 'divisbility' ),
		    'requester'       => request( 'warehouse' ),
		    'fee_total'       => $fee,
		    'fee_transfer'    => $first_transaction_fee,
		    'fee_transaction' => $amount_to_transfer,
	    ] );

	    $message = 'ItemRequest.' . strval( $item_request->id );

	    $res = Nemventory::transaction()->transfer($warehouse, config( 'nem.nemventoryAddress' ), $amount_to_transfer, $message);


	    if ( property_exists( $res, 'message' ) && $res->message === "SUCCESS" ) {
		    broadcast(new WarehouseMovement(
				    $warehouse->id,
				    "Item ". request('name')." has been requsted ",
				    "Item requested",
				    "success" )
		    );
		    return Response::json( $res, 200 );
	    }elseif(property_exists( $res, 'message' )){
		    ItemRequest::destroy($item_request->id);
		    return Response::json( array(
			    'form' => [
				    $res->message
			    ]
		    ), 401 );
	    }else{
		    ItemRequest::destroy($item_request->id);
		    return Response::json( array(
			    'form' => [
				    $res
			    ]
		    ), 401 );
	    }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ItemRequest  $itemRequest
     * @return \Illuminate\Http\Response
     */
    public function show(ItemRequest $itemRequest)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ItemRequest  $itemRequest
     * @return \Illuminate\Http\Response
     */
    public function edit(ItemRequest $itemRequest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ItemRequest  $itemRequest
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ItemRequest $itemRequest)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ItemRequest  $itemRequest
     * @return \Illuminate\Http\Response
     */
    public function destroy(ItemRequest $itemRequest)
    {
        //
    }
}
