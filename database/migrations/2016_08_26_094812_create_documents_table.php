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
            $table->dateTime(Document::RECEIVING_OR_ACTIVATION_DATE)->nullable();

            // PO Line
            $table->string(Document::PO_ID, 30)->nullable();

            $table->text('acquisition_method')->nullable();
            $table->text('additional_order_reference')->nullable();
            $table->text('reporting_code')->nullable();
            $table->text('receiving_note')->nullable();
            $table->text('receiving_status')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->text('vendor_code')->nullable();
            $table->text('source_type')->nullable();
            $table->text('fund_ledger_code')->nullable();
            $table->text('fund_ledger_name')->nullable();
            $table->text('fund_type')->nullable();
            $table->text('order_line_type_code')->nullable();
            $table->text('order_line_type')->nullable();

            $table->text('po_creator')->nullable();
            $table->text('po_modified_by')->nullable();
            $table->dateTime('po_creation_date')->nullable();
            $table->dateTime('po_modification_date')->nullable();
            $table->date('sent_date')->nullable();

            // Bibliographic data
            $table->text(Document::MMS_ID)->nullable();
            $table->text('title')->nullable();
            $table->text('edition')->nullable();
            $table->text('author')->nullable();
            $table->text('publisher')->nullable();
            $table->text('publication_date')->nullable();
            $table->text('publication_place')->nullable();
            $table->text('series')->nullable();
            $table->text('bibliographic_level')->nullable();
            $table->text('dewey_classification')->nullable();          // Should possibly be multi-valued
            $table->text('dewey_classification_top_line')->nullable();  // Should possibly be multi-valued
            $table->text('isbn')->nullable();         // Should possibly be multi-valued
            $table->text('base_status')->nullable();
            $table->text('material_type')->nullable();
            $table->text('biblio_modifiedby')->nullable();
            $table->dateTime('biblio_modified')->nullable();

            // Physical holding
            $table->text('holding_id')->nullable();
            $table->text('library_name')->nullable();
            $table->text('location_name')->nullable();
            $table->text('permanent_call_number')->nullable();
            $table->text('temporary_library_code')->nullable();
            $table->text('temporary_library_name')->nullable();
            $table->text('temporary_location_name')->nullable();

            // Physical item
            $table->string(Document::ITEM_ID, 30)->nullable()->unique();
            $table->text('item_policy')->nullable();
            $table->text('barcode')->nullable();
            $table->text('item_creator')->nullable();
            $table->text('process_type')->nullable();
            $table->date('item_creation_date')->nullable();
            $table->dateTime('receiving_date')->nullable();

            // Electronic portfolio
            $table->string(Document::PORTFOLIO_ID, 30)->nullable()->unique();
            $table->text('collection_name')->nullable();
            $table->date('portfolio_creation_date')->nullable();
            $table->date('activation_date')->nullable();

            // Enrichments
            $table->text('cover_image')->nullable();
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
