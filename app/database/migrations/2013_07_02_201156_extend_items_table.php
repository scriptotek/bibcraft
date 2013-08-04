<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ExtendItemsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('items', function(Blueprint $table) {

            $table->string('publisher');   // Add column
            $table->string('dewey');       // Add column
            $table->unique('recordid');    // Add index
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('items', function(Blueprint $table) {

            $table->dropColumn('publisher');
            $table->dropColumn('dewey');
            $table->dropUnique('items_recordid_unique');

        });
    }

}
