<?php

namespace App\Models;

use Nemventory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable {
	use HasApiTokens, Notifiable;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'name',
		'email',
		'password',
	];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
		'password',
		'remember_token',
	];

//	public function warehouses() {
	//		return $this->hasMany( 'App\Models\Warehouse' );
	//	}

	public function warehouses() {
		//return $this->belongsToMany( 'App\Models\Warehouse', 'user_warehouse', 'user_id', 'warehouse_id' )->wherePivot('relation', 'owner');
		return $this->belongsToMany( 'App\Models\Warehouse')->wherePivot('relation', 'owner');
	}

	public function allWarehouses() {
		//return $this->belongsToMany( 'App\Models\Warehouse', 'user_warehouse', 'user_id', 'warehouse_id' )->wherePivot('relation', 'owner');
		return $this->belongsToMany( 'App\Models\Warehouse');
	}
	public function vendors() {
		//return $this->belongsToMany( 'App\Models\Warehouse', 'user_warehouse', 'user_id', 'warehouse_id' )->wherePivot('relation', 'owner');
		return $this->belongsToMany( 'App\Models\Warehouse')->wherePivot('relation', 'vendor');
	}

	public function customers() {
		//return $this->belongsToMany( 'App\Models\Warehouse', 'user_warehouse', 'user_id', 'warehouse_id' )->wherePivot('relation', 'owner');
		return $this->belongsToMany( 'App\Models\Warehouse')->wherePivot('relation', 'customer');
	}

	private function privateKey( ) {
		return $this->hasMany( 'App\Models\PrivateKey');
	}

	public function getPrivateKey( $warehouseId ) {
		if($this->ownWarehouse($warehouseId)){
			return $this->privateKey()->where('warehouse_id', $warehouseId)->pluck('private_key');
		}
		throw new \Exception("Dont own warehouse to private key");
	}

	public function setPrivatekey( $warehouseId, $privateKeyString ) {
		if($this->hasWarehouse($warehouseId)){
			$privateKey = new PrivateKey();
			$privateKey->user_id = $this->id;
			$privateKey->warehouse_id = $warehouseId;
			$privateKey->private_key = encrypt($privateKeyString);
			$privateKey->save();
		}
	}

	public function hasPrivateKey( $warehouseId ) {
		if($this->ownWarehouse($warehouseId)){
			return $this->privateKey()->where('warehouse_id', $warehouseId)->exists();
		}
		return false;
	}

	public function ownWarehouse( $warehouseId ) {
		if($this->warehouses()->where('id', $warehouseId)->exists()){
			return true;
		}
		return false;
	}

	public function hasWarehouse( $warehouseId ) {
		if($this->allWarehouses()->where('id', $warehouseId)->exists()){
			return true;
		}
		return false;
	}



	public function itemRequests() {
		$itemRequests = [];

		foreach ( $this->warehouses()->get() as $warehouse ) {
			foreach ( $warehouse->itemRequests()->get() as $itemRequest ) {
				$itemRequests[] = $itemRequest;
			}
		}

		return $itemRequests;
	}


	public function purchaseOrders($id = null) {
//		return $this->hasManyThrough(
//			'App\Models\PurchaseOrder',
//			'App\Models\Warehouse',
//			'id', // Foreign key on users table...
//			'vendor_warehouse_id', // Foreign key on posts table...
//			'id', // Local key on countries table...
//			'id' // Local key on users table...
//		);
		$purchaseOrders = [];
		foreach ( $this->warehouses()->get() as $warehouse ) {
			foreach ( $warehouse->purchaseOrders()->get() as $purchaseOrder ) {
				$purchaseOrders[] = $purchaseOrder;
				if($id !== null ){
					if(intval($id) === $purchaseOrder->id){
						return $purchaseOrder;
					}
				}
			}
		}


		return $purchaseOrders;
	}

	public function salesOrders($id = null) {
		$salesOrders = [];
		foreach ( $this->warehouses()->get() as $warehouse ) {
			foreach ( $warehouse->salesOrders()->get() as $salesOrder ) {
				$salesOrders[] = $salesOrder;
				if($id !== null ){
					if(intval($id) === $salesOrder->id){
						return $salesOrder;
					}
				}
			}
		}

		return $salesOrders;
	}


	public function createWarehouse( $name, $relation, $address = null, $publicKey = null, $privateKey = null, $multisig = false) {
		if ( $address === null ) {
			//Create a new wallet for this account
			$new_account = Nemventory::helpers()->accountGenerate();
			$address     = $new_account->address;
			$publicKey   = $new_account->publicKey;
			$privateKey  = $new_account->privateKey;
		}

		$warehouse = $this->warehouses()->create( [
			'name'                => $name,
			'address'             => Nemventory::helpers()->addressPlain($address),
			'public_key'          => $publicKey,
			'multisig_public_key' => $multisig ? $multisig : null,
		], [
			'relation' => $relation
		]);

		if( $privateKey !== null )
			$this->setPrivatekey($warehouse->id,  $privateKey  );

		return $warehouse;
	}

	public function importWarehouse( $name, $relation , $address, $publicKey = null, $privateKey = null, $multisig = false) {

		$warehouse = $this->warehouses()->create( [
			'name'                => $name,
			'public_key'          => $publicKey,
			'address'             => $address,
			'multisig_public_key' => $multisig,
		],[
			'relation' => $relation
		]);

		if( $privateKey !== null )
			$this->setPrivatekey($warehouse->id, $privateKey );

		return $warehouse;
	}


	/**
	 * @param $address
	 *
	 * Either returns current warehouse or if account does not have that address in one of their warehouses, whill
	 * throw error.
	 */
//	public function hasWarehouse( $address ) {
//		try {
//			return $this->warehouses()->where( 'address', \NemSDK::models()->address( $address )->plain() )->first();
//		} catch ( \Exception $e ) {
//			throw new $e( "User dont own wallet" );
//		}
//	}
}
