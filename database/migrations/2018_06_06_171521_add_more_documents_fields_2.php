<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMoreDocumentsFields2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dateTime('bib_creation_date')->nullable();
            $table->dateTime('ready_at')->nullable();
            $table->text('item_or_portfolio_id')->nullable();
            $table->date('item_or_portfolio_creation_date')->nullable();
            $table->dropColumn('biblio_modified');
            $table->dropColumn('biblio_modifiedby');
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
            $table->dropColumn('bib_creation_date');
            $table->dropColumn('ready_at');
            $table->dropColumn('item_or_portfolio_id');
            $table->dropColumn('item_or_portfolio_creation_date');
            $table->dateTime('biblio_modified')->nullable();
            $table->text('biblio_modifiedby')->nullable();
        });
    }
}
