<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserWarehouse extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
	Schema::create('user_warehouse', function(Blueprint $table)
	{
		$table->integer('user_id')->unsigned()->index();
		$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
		$table->integer('warehouse_id')->unsigned()->index();
		$table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
		$table->text('relation');
	});
}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('user_warehouse');
	}
}
