<?php

namespace App\Http\Controllers;

use Nemventory;
use Illuminate\Http\Request;
use Response;

class FeeController extends Controller
{
	public function itemRequest(  ) {
		return Nemventory::fee()->itemRequest();
    }
}
