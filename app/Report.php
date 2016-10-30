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
        'name', 'querystring', 'created_by', 'updated_by', 'days_start', 'days_end',
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

    public function getDocumentsAttribute()
    {
        $startDate = Carbon::now()->subDays($this->days_start); // e.g. 30
        $endDate = Carbon::now()->subDays($this->days_end);     // e.g. 2

        return Document::query()
            ->whereRaw($this->querystring)
            ->where(Document::RECEIVING_OR_ACTIVATION_DATE, '>', $startDate->toDateString())
            ->where(Document::RECEIVING_OR_ACTIVATION_DATE, '<', $endDate->toDateString())
            ->orderBy(Document::RECEIVING_OR_ACTIVATION_DATE, 'desc')
            ->get();
    }

    public function getDocumentsByWeekAttribute()
    {
        $docs = $this->documents;

        $out = [];

        foreach ($docs as $doc) {
            $weekno = 'Uke ' . $doc->{Document::RECEIVING_OR_ACTIVATION_DATE}->format('W');
            $out[$weekno][] = $doc;
        }

        return $out;
    }

    public function getDocumentsByDeweyAttribute()
    {
        $docs = $this->documents;

        $out = [];

        foreach ($docs as $doc) {
            if (!$doc->dewey_classification) {
                $ddc = '1000 Not yet assigned';
                // continue
            } else {
                $ddc = substr($doc->dewey_classification, 0, 2) . '0';
            }
            $out[$ddc][] = $doc;
        }

        ksort($out);

        // sort trick
        $out['(Not yet assigned)'] = $out['1000 Not yet assigned'];
        unset($out['1000 Not yet assigned']);

        return $out;
    }
}
