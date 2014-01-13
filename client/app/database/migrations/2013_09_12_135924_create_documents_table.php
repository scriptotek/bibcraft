<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDocumentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('documents', function(Blueprint $table) {
            $table->increments('id');

            $table->string('bibsys_dokid')->unique();
			$table->string('bibsys_knyttid')->nullable()->unique();
			$table->string('bibsys_objektid');

            $table->string('isbn')->nullable();
            $table->string('publisher');
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->string('authors');
            $table->string('year');
			$table->string('cover')->nullable();
			$table->string('body')->nullable();
            $table->string('volume')->nullable();
            $table->string('series')->nullable();
            $table->string('dewey')->nullable();

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
		Schema::drop('documents');
	}

}
