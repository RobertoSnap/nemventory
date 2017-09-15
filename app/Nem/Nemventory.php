<?php

namespace App\Nem;

use NemSDK;

class Nemventory {

	public function __construct() {

	}

	public function helpers() {
		return new Helpers();
	}

	public function transaction() {
		return new Transaction();
	}

	public function inventory() {
		return new Inventory();
	}

	public function fee(  ) {
		return new Fee();
	}


}
