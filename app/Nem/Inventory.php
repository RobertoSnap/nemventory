<?php


namespace App\Nem;

use NemSDK;

class Inventory {
	public function getItemsFromAddress( $address ) {
		$mosaics_owned = NemSDK::account()->getMosaicCreatedByAddress( $address, config( 'nem.itemNamespace' ) );

		return $mosaics_owned;
	}

	public function getItems( $address, $onlyPositiveQuantitys = false ) {
		$mosaics_owned  = NemSDK::account()->getMosaicOwnedByAddress( $address );
		$items          = array();
		$new_item_index = 0;
		foreach ( $mosaics_owned as $key => $mosaic ) {
			if ( $mosaic->mosaicId->namespaceId === config( 'nem.itemNamespace' ) ) {
				if ( $onlyPositiveQuantitys && $mosaic->quantity === 0 ) {
					continue;
				}
				$items[ $new_item_index ]           = $mosaic->mosaicId;
				$items[ $new_item_index ]->quantity = $mosaic->quantity;
				$new_item_index ++;
			}
		}

		return $items;
	}

	public function getItemDetails( $id ) {
		preg_match( '/^([a-zA-Z\d]+)\.([a-zA-Z\d]+)\.([a-zA-Z_0-9]+)/', $id, $matches );
		if ( count( $matches ) > 3 ) {
			$itemName  = $matches[3];
			$namespace = $matches[1] . '.' . $matches[2];
		} else {
			$itemName  = $id;
			$namespace = config( 'nem.itemNamespace' );
		}
		$items = NemSDK::mosaic()->getMosaicDefinitions( $namespace, 1000 );
		foreach ( $items as $item ) {
			if ( $itemName == $item->mosaic->id->name ) {
				return $item;
			}
		}

		return false;
	}

	public function getAllItems() {
		$items = NemSDK::mosaic()->getMosaicDefinitions( config( 'nem.itemNamespace' ), 5000 );

		return $items;
	}


}