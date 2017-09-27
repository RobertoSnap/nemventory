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
		    \Auth::user()->importWarehouse(
		    	'Shared NEMventory warehouse',
			    'owner',
			    'TAKSUCAEKPVRQBUM6U35QV4IHWWWCC47FPKARL6C',
		    '2cd90e72d4de278c8dc1831bd03431a9a956ad4dd56bd23a9957925b8e151e71',
			    decrypt('eyJpdiI6InpsZ3IxdkRlQ2JwMUVMMnpcL0Y0RXN3PT0iLCJ2YWx1ZSI6InIzUlQwYVk2bVQ1blRURVZEYjNmb0poMDdcLzZPWHB2MTAwciswVEFcL3ZZV3RYM0lvYlVDNlB0ekRMbTh0R1AxVmFCTnE1enhKcW9xNFc0am1BNGdcL3FcL2x2a0F5MVZHRnIrWStXdllSNU9uST0iLCJtYWMiOiIyM2M4M2ZkYzg1MWM1M2M0ZjA1Y2Q2MTE3ZDdiNzFmMmQ0ZmNkYzk0NDFmNTZmZmU4OWMxYzFjNzU2YWY5MmZmIn0=')
			    );


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
