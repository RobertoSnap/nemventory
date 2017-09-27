<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/web';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:3|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {


        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        $warehouse = $user->createWarehouse($data['name'].'\'s first warehouse', 'owner');

	    $transaction = \NemSDK::transaction()->multisig(
		    env( 'MAIN_PUBLIC_ACCOUNT_PUBLIC_KEY' ),
		    env( 'MAIN_PUBLIC_ACCOUNT_PRIVATE_KEY' )
	    )->transfer(
		    $warehouse->address,
		    50000000,
		    "New account ".$data['name'],
		    env( 'MAIN_ACCOUNT_PUBLIC_KEY' ),
		    null
	    );

	    if(env('APP_ENV') === 'local'){
	        $mosaic_item = 'test2';
	    }else{
		    $mosaic_item = 'beginners_fishing_rod';
	    }
	    $res = \NemSDK::transaction()->multisig(
		    env( 'MAIN_PUBLIC_ACCOUNT_PUBLIC_KEY' ),
		    env( 'MAIN_PUBLIC_ACCOUNT_PRIVATE_KEY' )
	    )->transfer(
		    $warehouse->address,
		    1,
		    "New account items ".$data['name'],
		    env( 'MAIN_ACCOUNT_PUBLIC_KEY' ),
		    null,
		    array(
			    array(
				    'namespace' => config( 'nem.itemNamespace' ),
				    'mosaic'    => $mosaic_item,
				    'quantity'  => 3,
			    ),
		    )
	    );

	    return $user;
    }
}
