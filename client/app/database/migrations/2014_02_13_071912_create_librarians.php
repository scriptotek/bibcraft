<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLibrarians extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('librarians', function(Blueprint $table) {
			$table->increments('id');
			$table->string('name');
			$table->string('username');
			$table->string('password')->nullable();
			$table->string('activation_code');
			$table->boolean('superpowers');
			$table->dateTime('activated_at')->nullable();
			$table->dateTime('password_changed_at')->nullable();
			$table->timestamps();
			$table->dateTime('deleted_at')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('librarians');
	}

}
