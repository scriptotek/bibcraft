<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCollectionDocumentTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('collection_document', function(Blueprint $table) {
            $table->increments('id');
			$table->integer('collection_id')->unsigned();
			$table->integer('document_id')->unsigned();

			$table->unique(array('collection_id', 'document_id'));

			$table->foreign('collection_id')
				->references('id')->on('collections')
				->onDelete('cascade');
			$table->foreign('document_id')
				->references('id')->on('documents')
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
		Schema::drop('collection_document');
	}

}
