<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    const RECEIVING_OR_ACTIVATION_DATE = 'receiving_or_activation_date';
    const PO_ID = 'po_id';
    const MMS_ID = 'mms_id';
    const ITEM_ID = 'item_id';
    const PORTFOLIO_ID = 'portfolio_id';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        self::RECEIVING_OR_ACTIVATION_DATE,
        // 'sent_date',                 // date
        // 'item_creation_date',        // date
        'receiving_date',               // datetime
        'po_creation_date',             // datetime
        // 'activation_date',           // date
        //'portfolio_creation_date',    // date
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        self::PO_ID,
    ];

    /**
     * Canonical list of fields to be used for search
     *
     * @var array
     */
    protected static $fields = [

        // Sort date is made up from 'receiving date' for print or 'activation date' for electronic
        self::RECEIVING_OR_ACTIVATION_DATE,

        // PO Line
        self::PO_ID,
        'po_creator',
        'acquisition_method',
        'reporting_code',
        'receiving_note',
        'cancellation_reason',
        'vendor_code',
        'fund_ledger_code',
        'fund_ledger_name',
        'fund_type',
        'additional_order_reference',
        'order_line_type_code',
        'order_line_type',
        'po_modified_by',
        'receiving_status',
        'source_type',

        'po_creation_date',
        'po_modification_date',
        'sent_date',

        // Bibliographic data
        self::MMS_ID,
        'title',
        'edition',
        'author',
        'publisher',
        'publication_date',
        'publication_place',
        'series',
        'bibliographic_level',
        'dewey_classification',
        'dewey_classification_top_line',
        'isbn',
        'base_status',
        'material_type',
        'biblio_modified',
        'biblio_modifiedby',

        // Physical holding
        'holding_id',
        'library_name',
        'location_name',
        'permanent_call_number',
        'temporary_library_code',
        'temporary_library_name',
        'temporary_location_name',

        // Physical item
        'item_id',
        'item_policy',
        'barcode',
        'item_creator',  // also portfolio
        'process_type',
        'item_creation_date',
        'receiving_date',

        // Electronic portfolio
        'portfolio_id',
        'collection_name',
        'portfolio_creation_date',
        'activation_date',

        // Enrichments
        'cover_image',
        'description',
    ];

    /**
     * Get the related changes.
     */
    public function changes()
    {
        return $this->hasMany('App\Change');
    }

    public static function getFields()
    {
        $fields = self::$fields;  // In PHP arrays are assigned by copy, not by reference
        sort($fields);
        return $fields;
    }

    public function getPrimoLink()
    {
        $primoTpl = 'http://bibsys-almaprimo.hosted.exlibrisgroup.com/primo_library/libweb/action/dlSearch.do?institution=UBO&vid=UBO&tab=default_tab&query=any,contains,{mms_id}';

        return str_replace('{mms_id}', $this->{self::MMS_ID}, $primoTpl);
    }

    public function getDateString($field)
    {
        return isset($this->{$field}) ? $this->{$field}->toDateString() : '(unknown)';
    }

    public function __toString()
    {
        return (string) $this->title . ' (' . $this->author . ')';
    }

    public function repr()
    {
        return ($this->series ? '<div>' . $this->series . '</div>' : '') .
               ($this->author ? '<div>' . $this->author . '</div>' : '') .
               ($this->publication_date ? '<div>' . $this->publication_date .
                    ($this->edition ? ' (' . $this->edition . ')' : '') .
                '</div>' : '') ;
    }

    public function getComponentsAttribute()
    {
        return Document::where('mms_id', '=', $this->{self::MMS_ID})->get();
    }
}
