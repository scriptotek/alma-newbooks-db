<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;

class Report extends Model
{
    use SoftDeletes;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'querystring', 'created_by', 'updated_by',
    ];

    protected $casts = [
        'options' => 'json'
    ];

    /**
     * Get the user that created the report.
     */
    public function createdBy()
    {
        return $this->belongsTo('App\User', 'created_by');
    }

    /**
     * Get the user that last updated the report.
     */
    public function updatedBy()
    {
        return $this->belongsTo('App\User', 'updated_by');
    }

    /**
     * Get the document query builder.
     *
     * @return \App\DocumentBuilder
     */
    public function getDocumentBuilder()
    {
        $querystring = $this->querystring;

        // Expand %%only_first%% template
        $querystring = str_replace('%%only_first%%', '
            AND item_or_portfolio_id IN (
                SELECT DISTINCT ON (mms_id) item_or_portfolio_id
                FROM tilvekst_documents
                WHERE ' . str_replace('%%only_first%%', '', $querystring) .
                ' ORDER BY mms_id, item_or_portfolio_creation_date
            )', $querystring);

        return Document::query()
            ->where(function($query) use ($querystring) {
                return $query->whereRaw($querystring);
            });
    }

    public function getDocumentsAttribute()
    {
        return $this->getDocumentBuilder()
            ->orderBy(Document::RECEIVING_OR_ACTIVATION_DATE, 'desc')
            ->received()
            ->getUnique();
    }

    public function getLinkAttribute()
    {
        return action('ReportsController@show', ['id' => $this->id]);
    }
}
