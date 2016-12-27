<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMoreDocumentsFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->text('expected_activation_date')->nullable();
            $table->text('expected_receiving_date')->nullable();
            $table->text('note_to_vendor')->nullable();
            $table->dateTime('bib_modification_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn('expected_activation_date');
            $table->dropColumn('expected_receiving_date');
            $table->dropColumn('note_to_vendor');
            $table->dropColumn('bib_modification_date');
        });
    }
}
