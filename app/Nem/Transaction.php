<?php


namespace App\Nem;

use NemSDK;

class Transaction {

	public function transfer( $warehouse, $recipient, $amount, $message, $mosaics = null ) {
		switch ($warehouse->type()) {
		    case "simple":
			    return NemSDK::transaction()->transfer( $recipient, $amount, $message, $warehouse->public_key, decrypt( \Auth::user()->getPrivateKey($warehouse->id) ), $mosaics );
		        break;
		    case "multisig":
		        return NemSDK::transaction()->multisig($warehouse->public_key, decrypt( \Auth::user()->getPrivateKey($warehouse->id)) )->transfer($recipient, $amount, $message,$warehouse->multisig_public_key,null, $mosaics);
		        break;
		    case "limited":
				return 'Current warehouse is limited and cannot initaite transactions.';
		        break;
			default:
				return 'Could not determine Wallet type';
		}
	}


//	public function mosaic( Warehouse $warehouse, $name, $description, $namespace, $mosaic ) {
//		switch ($warehouse->type()) {
//			case "simple":
//
//				break;
//			case "multisig":
//				return \NemSDK::transaction()->multisig(
//					env('PUBLIC_ACCOUNT_PUBLIC_KEY'),
//					env('PUBLIC_ACCOUNT_PRIVATE_KEY')
//				)->mosaic(
//					$name,
//					$description,
//					config('nem.itemNamespace'),
//					env('OWNER_ACCOUNT_PUBLIC_KEY'),
//					[
//						'divisibility' => $this->item_request->divisibility,
//						'initialSupply' => $this->item_request->initial_stock,
//						'supplyMutable' => true,
//						'transferable' => true,
//					]
//				);
//				break;
//			case "limited":
//				return 'Current warehouse is limited and cannot initaite transactions.';
//				break;
//			default:
//				return 'Could not determine Wallet type';
//		}
//	}

	public function mainWallet(  ) {
		return array(
			'public_key'                => env('MAIN_PUBLIC_ACCOUNT_PUBLIC_KEY'),
			'private_key'               => env('MAIN_PUBLIC_ACCOUNT_PRIVATE_KEY'),
			'address'                   => env('OWNER_ACCOUNT_ADDRESS'),
			'multisig_public_key'       => env('MAIN_ACCOUNT_PUBLIC_KEY'),
		);
	}


}