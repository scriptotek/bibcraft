<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddCollectionDocumentTimestamps extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('collection_document', function(Blueprint $table) {
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
	    Schema::table('collection_document', function(Blueprint $table) {
			$table->dropColumn('created_at');
			$table->dropColumn('updated_at');
        });
	}

}
