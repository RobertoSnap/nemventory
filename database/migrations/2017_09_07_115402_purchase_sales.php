<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PurchaseSales extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
	public function up()
	{
		Schema::create('purchase_sales', function(Blueprint $table)
		{
			$table->integer('purchase_order_id')->unsigned()->index();
			$table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->onDelete('cascade');
			$table->integer('sales_order_id')->unsigned()->index();
			$table->foreign('sales_order_id')->references('id')->on('sales_orders')->onDelete('cascade');
		});
	}
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
	    Schema::dropIfExists('purchase_sales');
    }
}
