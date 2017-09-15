<?php

namespace App\Nem\Facades;

use Illuminate\Support\Facades\Facade;

class Nemventory extends Facade{

	protected static function getFacadeAccessor()
	{
		return 'Nemventory'; // the IoC binding.
	}

}