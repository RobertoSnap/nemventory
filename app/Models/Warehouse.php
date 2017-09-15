<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'name','public_key', 'address', 'multisig'
	];


	public function user(  ) {
		return $this->belongsTo('App\Models\User');
	}

	public function type(  ) {

		if( ! \Auth::user()->hasPrivateKey($this->id) || $this->public_key === null ){
			return "limited";
		}else if($this->multisig_public_key !== null){
			return "multisig";
		}else{
			return "simple";
		}
	}

	public function purchaseOrders(  ) {
		return $this->hasMany(PurchaseOrder::class, 'customer_warehouse_id');
	}

	public function salesOrders(  ) {
		return $this->hasMany(SalesOrder::class, 'vendor_warehouse_id');
	}



}
