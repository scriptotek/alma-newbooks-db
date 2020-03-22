<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    const RECEIVING_OR_ACTIVATION_DATE = 'receiving_or_activation_date';
    const SENT_DATE = 'sent_date';
    const PO_ID = 'po_id';
    const MMS_ID = 'mms_id';
    const ITEM_ID = 'item_id';
    const PORTFOLIO_ID = 'portfolio_id';

    const PO_CREATOR = 'po_creator';

    const REPORTING_CODE = 'reporting_code';
    const REPORTING_CODE_SECONDARY = 'reporting_code_secondary';
    const REPORTING_CODE_TERTIARY = 'reporting_code_tertiary';

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'cover',
        'primo_link',
        'self_link',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at', // Eloquent model
        'updated_at', // Eloquent model
        self::RECEIVING_OR_ACTIVATION_DATE,
        // 'sent_date',                 // date
        // 'item_creation_date',        // date
        'receiving_date',               // datetime
        'po_creation_date',             // datetime
        'bib_creation_date',            // datetime
        'bib_modification_date',        // datetime
        // 'activation_date',           // date
        //'portfolio_creation_date',    // date
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'receiving_date' => 'datetime:Y-m-d H:i:s',
        'activation_date' => 'datetime:Y-m-d H:i:s',
        'receiving_or_activation_date' => 'datetime:Y-m-d H:i:s',

        'bib_creation_date' => 'date:Y-m-d',
        'portfolio_creation_date' => 'date:Y-m-d',
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
        // 'po_creator',  // Hide because of privacy issues
        'acquisition_method',
        self::REPORTING_CODE,
        self::REPORTING_CODE_SECONDARY,
        self::REPORTING_CODE_TERTIARY,
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
        'note_to_vendor',
        'expected_receiving_date',
        'expected_activation_date',

        'po_creation_date',
        'po_modification_date',
        self::SENT_DATE,

        // Bibliographic data
        self::MMS_ID,
        'bib_creation_date',
        'bib_modification_date',
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

        // Physical holding
        'holding_id',
        'library_name',
        'location_name',
        'permanent_call_number',
        'permanent_call_number_type',
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
     * Create a new Eloquent query builder for the model.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return \App\DocumentBuilder
     */
    public function newEloquentBuilder($query)
    {
        return new DocumentBuilder($query);
    }

    /**
     * Get the related changes.
     */
    public function changes()
    {
        return $this->hasMany('App\Change');
    }

    public function getCoverAttribute()
    {
        return null; // @TODO: Get URL from cover service
    }

    public static function getFields($includeSensitive=false)
    {
        $fields = self::$fields;  // In PHP arrays are assigned by copy, not by reference
        if ($includeSensitive) {
            $fields[] = self::PO_CREATOR;
        }
        sort($fields);
        return $fields;
    }

    public function getPrimoLinkAttribute()
    {
        $primoTpl = 'https://bibsys-almaprimo.hosted.exlibrisgroup.com/primo-explore/search?tab=default_tab&sortby=rank&vid=UIO&lang=no_NO&query=any,contains,{mms_id}';

        return str_replace('{mms_id}', $this->{self::MMS_ID}, $primoTpl);
    }

    public function getSelfLinkAttribute()
    {
        return action('DocumentsController@show', ['document' => $this->id]);
    }

    public function getDateString($field)
    {
        return isset($this->{$field}) ? $this->{$field}->toDateString() : '(unknown)';
    }

    public function __toString()
    {
        return (string) $this->title . ' (' . $this->author . ')';
    }

    public function getComponentsAttribute()
    {
        return Document::where('mms_id', '=', $this->{self::MMS_ID})->orderBy('item_creation_date')->get();
    }

    public function link_to_date($dateField)
    {
        if (!isset($this->{$dateField})) {
            return '-----';
        }
        $val = $this->{$dateField};
        if (!is_object($val)) {
            $val = new \Carbon\Carbon($val);
        }
        $val2 = $val->copy()->addDay();

        $url = action('DocumentsController@index', [
            'k1' => $dateField,
            'r1' => 'gte',
            'v1' => $val->toDateString(),
            'k2' => $dateField,
            'r2' => 'lt',
            'v2' => $val2->toDateString(),
        ]);

        return '<a href="' . $url . '">' . $this->{$dateField} . '</a>';
    }

    public function toArrayUsingTemplate(Template $template = null)
    {
        if (is_null($template)) {
            return $this->toArray();
        }
        return [
            'title'              => $this->title,
            'link'               => $this->getPrimoLinkAttribute(),
            'description'        => $template->render($this),
            'date'               => $this->{Document::RECEIVING_OR_ACTIVATION_DATE} ? $this->{Document::RECEIVING_OR_ACTIVATION_DATE}->toDateTimeString() : null,
        ];
    }
}
