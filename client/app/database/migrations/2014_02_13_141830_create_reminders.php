<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateReminders extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('reminders', function(Blueprint $table) {
			$table->increments('id');
			$table->string('body');
			$table->timestamps();
		});

		Schema::create('loan_reminder', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('loan_id')->unsigned();
			$table->integer('reminder_id')->unsigned();
			$table->unique(array('loan_id', 'reminder_id'));

			$table->foreign('loan_id')
				->references('id')->on('loans')
				->onDelete('cascade');
			$table->foreign('reminder_id')
				->references('id')->on('reminders')
				->onDelete('cascade');
		});

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('loan_reminder');
		Schema::drop('reminders');
	}

}
