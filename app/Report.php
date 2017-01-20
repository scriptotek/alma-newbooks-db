<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Report extends Model
{
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
        return Document::query()
            ->where(function($query) {
                return $query->whereRaw($this->querystring);
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
