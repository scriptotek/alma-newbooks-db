<?php

use App\Document;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {

            $table->increments('id');

            // Sort date made up from 'receiving date' for print or 'activation date' for electronic
            $table->date(Document::RECEIVING_OR_ACTIVATION_DATE)->nullable();

            // PO Line
            $table->string(Document::PO_ID)->nullable()->unique();

            $table->string('acquisition_method')->nullable();
            $table->string('additional_order_reference')->nullable();
            $table->string('reporting_code')->nullable();
            $table->string('receiving_note')->nullable();
            $table->string('receiving_status')->nullable();
            $table->string('cancellation_reason')->nullable();
            $table->string('vendor_code')->nullable();
            $table->string('source_type')->nullable();
            $table->string('fund_ledger_code')->nullable();
            $table->string('fund_ledger_name')->nullable();
            $table->string('fund_type')->nullable();
            $table->string('order_line_type_code')->nullable();
            $table->string('order_line_type')->nullable();

            $table->string('po_creator')->nullable();
            $table->string('po_modified_by')->nullable();
            $table->dateTime('po_creation_date')->nullable();
            $table->dateTime('po_modification_date')->nullable();
            $table->date('sent_date')->nullable();

            // Bibliographic data
            $table->string(Document::MMS_ID)->nullable();
            $table->text('title')->nullable();
            $table->string('edition')->nullable();
            $table->string('author')->nullable();
            $table->string('publisher')->nullable();
            $table->string('publication_date')->nullable();
            $table->string('publication_place')->nullable();
            $table->string('series')->nullable();
            $table->string('bibliographic_level')->nullable();
            $table->string('dewey_classification')->nullable();          // Should possibly be multi-valued
            $table->string('dewey_classification_top_line')->nullable();  // Should possibly be multi-valued
            $table->string('isbn')->nullable();         // Should possibly be multi-valued
            $table->string('base_status')->nullable();
            $table->string('material_type')->nullable();
            $table->string('biblio_modifiedby')->nullable();
            $table->dateTime('biblio_modified')->nullable();

            // Physical holding
            $table->string('holding_id')->nullable();
            $table->string('library_name')->nullable();
            $table->string('location_name')->nullable();
            $table->string('permanent_call_number')->nullable();
            $table->string('temporary_library_code')->nullable();
            $table->string('temporary_library_name')->nullable();
            $table->string('temporary_location_name')->nullable();

            // Physical item
            $table->string('item_id')->nullable();
            $table->string('item_policy')->nullable();
            $table->string('barcode')->nullable();
            $table->string('item_creator')->nullable();
            $table->string('process_type')->nullable();
            $table->dateTime('item_creation_date')->nullable();
            $table->dateTime('receiving_date')->nullable();

            // Electronic portfolio
            $table->string('portfolio_id')->nullable();
            $table->string('collection_name')->nullable();
            $table->date('portfolio_creation_date')->nullable();
            $table->date('activation_date')->nullable();

            // Enrichments
            $table->string('cover_image')->nullable();
            $table->text('description')->nullable();
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
