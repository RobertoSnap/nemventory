<?php

namespace App\Jobs;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportTransactions implements ShouldQueue
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
		\Log::info( 'ImportIncomingTransactions started running' );

		//Todo: start from last hash

		$nem_transactions = \NemSDK::account()->allTransactions( config( 'nem.nemventoryAddress' ) );

		foreach ( $nem_transactions as $nem_transaction ) {

			$transaction = ( new Transaction() )->updateOrCreate( [
					'id' => $nem_transaction->meta->id
				]
				, [
					'hash'        => $nem_transaction->meta->hash->data,
					'height'      => $nem_transaction->meta->height,
					'type'        => $nem_transaction->transaction->type,
					'transaction' => json_encode( $nem_transaction->transaction ),
				] );
		}

		\Log::info( 'ImportIncomingTransactions stopped running' );
	}
}
