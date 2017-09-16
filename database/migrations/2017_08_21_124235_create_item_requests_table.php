<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_requests', function (Blueprint $table) {
            $table->increments('id');
	        $table->integer('warehouse_id');
	        $table->string('name');
	        $table->text('description');
	        $table->text('status');
	        $table->integer('divisbility');
	        $table->integer('initial_stock');
	        $table->integer('fee_total');
	        $table->integer('fee_transfer');
	        $table->integer('fee_transaction');
	        $table->text('requester');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('item_requests');
    }
}
