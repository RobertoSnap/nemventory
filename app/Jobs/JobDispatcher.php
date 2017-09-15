<?php

namespace App\Jobs;

use App\Events\WarehouseMovement;
use Nemventory;
use DB;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class JobDispatcher //implements ShouldQueue
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
		\Log::info( 'JobDispatcher started running' );

		$this->dispatchJobsFromTransactions();

		$this->transferItemRequestBack();

		\Log::info( 'JobDispatcher stopped running' );


	}

	public function dispatchJobsFromTransactions() {
		$t_registered = DB::table( 'transactions' )->where( 'status', 'registered' )->get();
		foreach ( $t_registered as $t ) {
			$t->transaction = json_decode( $t->transaction );
			//Check if multisig
			if($t->transaction->type === 4100 && property_exists($t->transaction, 'otherTrans') ){
				//It is multisig, so set the transaction
				$t->transaction = $t->transaction->otherTrans;
			}
			if(property_exists($t->transaction, 'message') && property_exists($t->transaction->message, 'payload')){
				$message        = Nemventory::helpers()->hexToStr( $t->transaction->message->payload );
				switch ( true ) {
					case preg_match( '/^(ItemRequest)([^abc])(\d+)$/', $message, $matches, PREG_OFFSET_CAPTURE, 0 ):
						//Initiate a job to start creating to mosaic if the amounts match
						dispatch( new CreateItem( $t, intval( $matches[3][0] ) ) );
						break;
					default:
						//Transaction has no information that implies it should do a job so mark it as processed.
						DB::table( 'transactions' )
						  ->where( 'id', $t->id )
						  ->update( [
							  'status'  => 'processed',
							  'comment' => 'Processed by JobDispatcher, no job issued.'
						  ] );
				}
			}
		}
	}

	public function transferItemRequestBack() {
		$ir_onChain = DB::table( 'item_requests' )->where( 'status', 'onChain' )->get();
		foreach ( $ir_onChain as $t ) {
			$nem_mosaic = Nemventory::inventory()->getItemDetails( $t->name );
			if ( $nem_mosaic && $nem_mosaic->mosaic->id->name === $t->name ) {
				$res = \NemSDK::transaction()->multisig(
					env( 'MAIN_PUBLIC_ACCOUNT_PUBLIC_KEY' ),
					env( 'MAIN_PUBLIC_ACCOUNT_PRIVATE_KEY' )
				)->transfer(
					$t->requester,
					1,
					"Item request " . $t->id,
					env( 'MAIN_ACCOUNT_PUBLIC_KEY' ),
					null,
					array(
						array(
							'namespace' => config( 'nem.itemNamespace' ),
							'mosaic'    => $t->name,
							'quantity'  => $t->initial_stock,
						),
					)
				);

				if ( $res->message === "SUCCESS" ) {
					DB::table( 'item_requests' )
					  ->where( 'id', $t->id )
					  ->update( [
						  'status' => 'inTransfer',
					  ] );

					broadcast(new WarehouseMovement(
							$t->warehouse_id,
							"Your requsted item: ". request('name')." are beeing sent to your account. Stay tuned. ",
							"Item assembly",
							"info" )
					);
				} else {
					DB::table( 'item_requests' )
					  ->where( 'id', $t->id )
					  ->update( [
						  'status' => 'error',
					  ] );
					\Log::alert( "Item request " . $t->id . " encountered a problem transferring back. Message: " . $res->message );
					broadcast(new WarehouseMovement(
							$t->warehouse_id,
							"Your requsted item: ". request('name')." has an error during transfer to your account. An administrator has been notified. ",
							"Item assembly",
							"danger" )
					);
				}

			}
		}
	}
}
