<?php

use Illuminate\Database\Seeder;

use Faker\Factory as Faker;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
	    DB::table('users')->insert([
		    'name' => 'Robin',
		    'email' => 'rpe@outlook.com',
		    'password' => bcrypt('123'),
	    ]);
	    DB::table('users')->insert([
		    'name' => '1',
		    'email' => '1@1.com',
		    'password' => bcrypt('123'),
	    ]);
	    DB::table('warehouses')->insert([
		    'name' => 'NEMventory',
		    'public_key' => '6d8154222f39921d4a4a30eb18a9e749db253e08ebecfbf6ea228a2d5771d246',
		    'address' => 'TDCXNEVVGGGLBJSB2HQO7F2G4NLSXCMUJ3GVUQ7F',
		    'multisig_public_key' => 'ae7677a25bfaf29b3887f21a87492bd872441cbb208e5eacd3ee08656950f412'
	    ]);
	    DB::table('warehouses')->insert([
		    'name' => 'Warehouse 1',
		    'public_key' => '1471bf727426edbb892323e76676e3c3dc4f2c4910e089dd221034aade731ca0',
		    'address' => 'TDRUFSZUYKVLAEC5UKV7US6XNI2XPBT4FSC7DTFI',
	    ]);
	    DB::table('warehouses')->insert([
		    'name' => 'Secure Warehouse 3',
		    'public_key' => 'b8c3df610a1bd804fce51c48c5cde0436f86c9427eeb0ea2b0773d7823020849',
		    'address' => 'TCFUWU42GFZ3C3UIWQK4KA6RSXTE7MZRZ2SBCENI',
		    'multisig_public_key' => 'ad556fc53e4638a1d8aa14ee039f0e3700156fb8d18e97c4978c04c8a76a2813'
	    ]);
	    DB::table('warehouses')->insert([
		    'name' => 'Good Clothes Factory',
		    'public_key' => '1b954dd80d8b4056f21bf09e7be3409160ac3a352db3dc502fb792198ff61407',
		    'address' => 'TBSMCD7O5JZ2B5PHA3YNFOE2DS5KKS3HEX2C6AZN',
	    ]);
	    DB::table('warehouses')->insert([
		    'name' => 'Mega shop',
		    'public_key' => 'c19c36e842a8667f150e36938be722442bd8c287254cb4b08d213007522dfc85',
		    'address' => 'TCIGE4CHV7MDH3IGJZ6BT43Y6OBIIUOL3TYRFALH',
	    ]);


	    DB::table('private_keys')->insert([
		    'user_id' => '1',
		    'warehouse_id' => '1',
		    'private_key' => encrypt('8fb6e1c2cd8e4fceb663665d4fb7171e73c0cca6f5a6f675c68be127a7396b63'),
	    ]);
	    DB::table('private_keys')->insert([
		    'user_id' => '1',
		    'warehouse_id' => '2',
		    'private_key' => encrypt('7095b7d87d5e293d009ee9160cbc4f25c9b858ad4e124a556aec724502c1ba7e'),
	    ]);
	    DB::table('private_keys')->insert([
		    'user_id' => '1',
		    'warehouse_id' => '3',
		    'private_key' => encrypt('b5d7ece5f66e051deeda5470d88c92d5a2b66eae42f4ca29c65b3ae6dd3d07a2'),
	    ]);
	    DB::table('private_keys')->insert([
		    'user_id' => '2',
		    'warehouse_id' => '4',
		    'private_key' => encrypt('00b7a822c9d178c87e8016cb058a70531dbef81fac2fa1ac9dc2a1653ddbf10244'),
	    ]);
	    DB::table('private_keys')->insert([
		    'user_id' => '2',
		    'warehouse_id' => '5',
		    'private_key' => encrypt('00a7b41cc5bb5d279624f6bdd6dbbecdd37d07ad90efca9ee2b958a6f38494b787'),
	    ]);


	    DB::table('purchase_orders')->insert([
		    'id' => '1',
		    'customer_warehouse_id' => 1,
		    'vendor_warehouse_id' => 2,
		    'comment' => 'Testign PO',
	    ]);
	    DB::table('sales_orders')->insert([
		    'id' => '1',
		    'customer_warehouse_id' => 1,
		    'vendor_warehouse_id' => 2,
		    'comment' => 'Testign PO',
	    ]);

	    \App\Models\User::find(1)->warehouses()->attach([1,2,3],['relation' => "owner"]);
	    \App\Models\User::find(1)->warehouses()->attach([4],['relation' => "owner"]);
	    \App\Models\User::find(1)->warehouses()->attach([5],['relation' => "customer"]);
	    \App\Models\User::find(2)->warehouses()->attach([4],['relation' => "owner"]);




    }
}
