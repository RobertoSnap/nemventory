<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrivateKey extends Model
{
	protected $hidden = [
		'private_key'
	];

	public function user(  ) {
		return $this->belongsTo('App\Models\User');
	}
}
