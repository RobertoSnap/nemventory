<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
	protected $guarded = [];

	public function warehouse(  ) {
		return $this->belongsTo('App\Models\Warehouse');
	}

	public function lines(  ) {
		return $this->hasMany(PurchaseOrderLine::class);
	}

	public function salesOrder(  ) {
		return $this->hasOne('App\Models\PurchaseOrder');
	}
}
