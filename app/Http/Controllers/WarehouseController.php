<?php

namespace App\Http\Controllers;

use App\Events\UserMovement;
use App\Events\WarehouseMovement;
use App\Models\Warehouse;
use Nemventory;
use DB;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WarehouseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
	    if ( Request( 'byId' ) === "true" ) {
		    return  $request->user()->warehouses()->get()->keyBy( 'id' );
	    }
		return $request->user()->warehouses()->get();
    }

	public function vendor(Request $request)
	{
		if ( Request( 'byId' ) === "true" ) {
			return $request->user()->vendors()->get()->keyBy( 'id' );
		}
		return $request->user()->vendors()->get();
	}

	public function vendorImport( Request $request ) {
		$this->validate( $request, [
			'address' => array(
				'required',
				'regex: /^[a-zA-Z0-9-]+$/',
			),
		] );
		$address =  Nemventory::helpers()->addressPlain(Request( 'address' ));;
		$vendor_warehouse = DB::table('warehouses')->where('address', $address)->first();

		if($vendor_warehouse !== null){
			//Attach the allready exsisting warehouse to user
			\Auth::user()->warehouses()->attach( $vendor_warehouse->id, [ 'relation' => 'vendor' ] );
			$vendor_warehouse = "ok";
		}else {
			//Create new user
			$this->validate( $request, [
				'name' => 'string|required',
			] );
			$vendor_warehouse = \Auth::user()->createWarehouse( request( 'name' ), 'vendor', $address, null, null, null );
		}

		broadcast(new UserMovement(
				\Auth::id(),
				"Vendor ".request( 'name' )." was created.",
				"New vendor",
				"success",
				true)
		);

		return $vendor_warehouse;
	}

	public function vendorCreate( Request $request ) {
		$this->validate( $request, [
			'name' => 'string|required',
		] );

		$vendor_warehouse = \Auth::user()->createWarehouse( request( 'name' ), 'vendor' );

		broadcast(new UserMovement(
				\Auth::id(),
				"Vendor ".request( 'name' )." was created.",
				"New vendor",
				"success",
				true)
		);

		return $vendor_warehouse;
	}

	public function customer(Request $request)
	{
		if ( Request( 'byId' ) === "true" ) {
			return $request->user()->customers()->get()->keyBy( 'id' );
		}
		return $request->user()->customers()->get();
	}

	public function customerImport( Request $request ) {
		$this->validate( $request, [
			'address' => array(
				'required',
				'regex: /^[a-zA-Z0-9-]+$/',
			),
		] );
		$address = Nemventory::helpers()->addressPlain(Request( 'address' ));
		$warehouse = DB::table('warehouses')->where('address', $address)->first();

		if($warehouse !== null){
			//Attach the allready exsisting warehouse to user
			\Auth::user()->warehouses()->attach( $warehouse->id, [ 'relation' => 'customer' ] );
			$warehouse = "ok";
		}else {
			//Create new user
			$this->validate( $request, [
				'name' => 'string|required',
			] );
			$warehouse = \Auth::user()->createWarehouse( request( 'name' ), 'customer', $address, null, null, null );
		}
		broadcast(new UserMovement(
				\Auth::id(),
				"Customer ".request( 'name' )." was imported.",
				"New customer",
				"success",
				true)
		);

		return $warehouse;
	}

	public function customerCreate( Request $request ) {
		$this->validate( $request, [
			'name' => 'string|required',
		] );

		$warehouse = \Auth::user()->createWarehouse( request( 'name' ), 'customer' );

		broadcast(new UserMovement(
				\Auth::id(),
				"Customer ".request( 'name' )." was created.",
				"New customer",
				"success",
				true)
		);

		return $warehouse;
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
	    $this->validate( $request, [
		    'name' => 'string|required|max:32',
	    ] );

	    $warehouse = \Auth::user()->createWarehouse( request( 'name' ), 'owner'  );

	    broadcast(new UserMovement(
			    \Auth::id(),
			    "Warehouse ".$warehouse->name." was created.",
			    "New warehouse",
			    "success",
			    true)
	    );

	    return \Response::json($warehouse);
    }


	public function import( Request $request ) {
		$this->validate( request(), [
			'type' => [
				'required',
				Rule::in(['multisig', 'cosigner', 'simple']),
			],
		] );
		if($request->type === 'simple'){
			$this->validate( request(), [
				'name' => 'string|required|max:32',
				'address'    => array(
					'regex:/[a-zA-Z0-9]+/',
					'required'
				),
			] );
			$warehouse = \Auth::user()->importWarehouse(
				request( 'name' ),
				'owner',
				request( 'address' )
			);
		}else{
			$this->validate( request(), [
				'name' => 'string|required|max:32',
				'multisig' => 'array|required',
				'multisig.address' => 'string|required',
				'multisig.publicKey' => 'string|required',
				'cosigner' => 'array|required',
				'cosigner.publicKey' => 'string|required',
				'cosignerPrivateKey' => 'string|size:64',
			] );

			$warehouse = \Auth::user()->importWarehouse(
				request( 'name' ),
				'owner',
				request( 'multisig.address' ),
				request( 'cosigner.publicKey' ),
				request( 'cosignerPrivateKey' ),
				request( 'multisig.publicKey' )
			);
			broadcast(new UserMovement(
					\Auth::id(),
					"Warehouse ".$warehouse->name." was imported.",
					"New warehouse",
					"success",
					true)
			);
		}
		return \Response::json($warehouse);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Warehouse  $warehouse
     * @return \Illuminate\Http\Response
     */
    public function show(Warehouse $warehouse)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Warehouse  $warehouse
     * @return \Illuminate\Http\Response
     */
    public function edit(Warehouse $warehouse)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Warehouse  $warehouse
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Warehouse $warehouse)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Warehouse  $warehouse
     * @return \Illuminate\Http\Response
     */
    public function destroy(Warehouse $warehouse)
    {
        //
    }
}
