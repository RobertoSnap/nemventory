<?php

namespace App\Jobs;


use App\Models\Item;
use Nemventory;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class FetchNemItems implements ShouldQueue
	{
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

	/**
	 * Create a new job instance.
	 *
	 * @return void
	 */
	public function __construct() {
		//
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle() {
		\Log::info( 'FetchNemItems started running' );
		$items = Nemventory::inventory()->getAllItems();
		foreach ( $items as $item ) {
			$divisibility   = null;
			$initial_supply = null;
			foreach ( $item->mosaic->properties as $key => $value ) {
				switch ( $value->name ) {
					case 'divisibility':
						$divisibility = $value->value;
						break;
					case 'initialSupply':
						$initial_supply = $value->value;
						break;
				}
			}
			$item = (new Item)->updateOrCreate( [
					'nem_id' => $item->meta->id, 'namespace_id'   => $item->mosaic->id->namespaceId,
				]
				,[
					'nem_id'         => $item->meta->id,
					'creator'        => $item->mosaic->creator,
					'name'           => $item->mosaic->id->name,
					'namespace_id'   => $item->mosaic->id->namespaceId,
					'description'    => $item->mosaic->description,
					'divisbility'    => $divisibility,
					'initial_supply' => $initial_supply,

				] );
		}
		\Log::info('FetchNemItems finished running');
	}

}
