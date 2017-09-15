<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesOrder extends Model
{
	protected $guarded = [];

	public function warehouse(  ) {
		return $this->belongsTo('App\Models\Warehouse');
	}

	public function lines(  ) {
		return $this->hasMany(SalesOrderLine::class);
	}

}
