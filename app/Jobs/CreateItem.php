<?php

namespace App\Jobs;

use App\Events\WarehouseMovement;
use DB;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CreateItem //implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $id;
    protected $transaction;
    protected $item_request;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($transaction, $item_request_id)
    {
		$this->id = $item_request_id;
		$this->transaction = $transaction;
		$this->item_request = DB::table('item_requests')->where('id', $this->id)->first();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
    	if($this->item_request === null){
    	    //No item request for this NEM transactions
		    DB::table('transactions')
		      ->where('id', $this->transaction->id)
		      ->update([
			      'status' => 'processed',
			      'comment' => 'Sent to Create item, but not item request found.'
		      ]);

		    return;
	    }
	    //Check that the fee is enough matches
	    switch ( true ) {
	        case $this->transaction->transaction->amount >= $this->item_request->fee_transaction  :
	            //We got enough to start creating the mosaic

		        $res = \NemSDK::transaction()->multisig(
			        env('MAIN_PUBLIC_ACCOUNT_PUBLIC_KEY'),
			        env('MAIN_PUBLIC_ACCOUNT_PRIVATE_KEY')
		        )->mosaic(
			        $this->item_request->name,
			        $this->item_request->description,
			        config('nem.itemNamespace'),
			        env('MAIN_ACCOUNT_PUBLIC_KEY'),
			        [
				        'divisbility' => $this->item_request->divisbility,
				        'initialSupply' => $this->item_request->initial_stock,
				        'supplyMutable' => true,
				        'transferable' => true,
			        ]
		        );

	            //Update progress in DB
				if($res->message === "SUCCESS"){
					DB::table('transactions')
					  ->where('id', $this->transaction->id)
					  ->update([
						  'status' => 'created',
						  'comment' => 'Item created on blockchain with hash outer/inner '.$res->transactionHash->data.'/'.$res->innerTransactionHash->data,
					  ]);

					DB::table('item_requests')
					  ->where('id', $this->item_request->id)
					  ->update([
						  'status' => 'onChain',
					  ]);
					broadcast(new WarehouseMovement(
							$this->item_request->warehouse_id,
							"Your requsted item: ". request('name')." is now beeing assembled on the NEMventory account. Stay tuned. ",
							"Item assembly",
							"info" )
					);

				}else{
					DB::table('transactions')
					  ->where('id', $this->transaction->id)
					  ->update([
						  'status' => 'error',
						  'comment' => 'Error when creating item. Data: '.json_encode($res),
					  ]);
					DB::table('item_requests')
					  ->where('id', $this->item_request->id)
					  ->update([
						  'status' => 'error',
					  ]);
					broadcast(new WarehouseMovement(
							$this->item_request->warehouse_id,
							"Your requsted item: ". request('name')." has an error during assembly. An administrator has been notified. ",
							"Item assembly",
							"danger" )
					);
				};

	            break;
	        case $this->transaction->transaction->amount < $this->item_request->fee_transaction :
	            //We do not have enough to continue with the transaction.
		        DB::table('transactions')
		          ->where('id', $this->transaction->id)
		          ->update([
			          'status' => 'funds',
			          'comment' => 'Sent to Create item, but amount transferred was not enough.'
		          ]);

		        broadcast(new WarehouseMovement(
				        $this->item_request->warehouse_id,
				        "Your requsted item: ". request('name')." did not have enough funds to complete assembly. Please contact administrator. ",
				        "Item assembly",
				        "danger" )
		        );
				//todo: notify the user and ask them to transfer more, maybe transfer back.
	            break;
	        default:
		        DB::table('transactions')
		          ->where('id', $this->transaction->id)
		          ->update([
			          'status' => 'error',
			          'comment' => 'Sent to Create item, but something wrong with the amounts.'
		          ]);
	            break;
	    };




    }
}
