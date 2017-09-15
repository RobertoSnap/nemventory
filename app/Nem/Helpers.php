<?php


namespace App\Nem;

use NemSDK;

class Helpers {


	public function pretty_addresses( $wallets ) {
		foreach ( $wallets as $wallet ) {
			$wallet['address'] = \NemSDK::models()->address($wallet['address'])->pretty();
		};

		return $wallets;
	}

	public function itemRequestFee(  ) {
		$total_fee = 0;

		//Transaction to send to nemventory
		$total_fee += 0.1;

		//Transaction to crate the mosaic
		$total_fee += 10.3;

		//Transaction to send the mosaic to warehouse
		$total_fee += 1.2;

		return $total_fee;
	}

	public function hexToStr( $message ) {
		return NemSDK::models()->transaction()->message()->hexToStr($message);
	}

	public function accountGenerate() {
		return NemSDK::models()->account()->generate();
	}

	public function toMicroXem( $amount ) {
		return NemSDK::models()->xem()->toMicroxem( $amount );
	}

	public function addressPlain ($address) {
		return \NemSDK::models()->address($address)->plain();
	}

	public function addressPretty ($address) {
		return \NemSDK::models()->address($address)->pretty();
	}



}