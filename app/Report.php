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

    public function getDocumentsAttribute()
    {
        // $startDate = isset($this->days_start) ? Carbon::now()->subDays($this->days_start) : null; // e.g. 30
        // $endDate = isset($this->end_date) ? Carbon::now()->subDays($this->days_end) : null;     // e.g. 2

        return $this->getDocuments();
    }

    public function getDocumentsFromMonth($year, $month)
    {
        $startDate = Carbon::create($year, $month, 1);
        $endDate = $startDate->copy()->addMonth();

        return $this->getDocuments($startDate, $endDate);
    }

    public function getDocumentsFromWeek($year, $week)
    {
        $startDate = new Carbon("{$year}W{$week}");
        $endDate = $startDate->copy()->addWeek();

        return $this->getDocuments($startDate, $endDate);
    }

    public function getDocuments($startDate=null, $endDate=null)
    {
        $query = Document::query()
            ->where(function($query) {
                return $query->whereRaw($this->querystring);
            });
        if (!is_null($startDate)) {
            $query = $query->where(Document::RECEIVING_OR_ACTIVATION_DATE, '>=', $startDate->toDateString());
        }
        if (!is_null($endDate)) {
            $query = $query->where(Document::RECEIVING_OR_ACTIVATION_DATE, '<', $endDate->toDateString());
        }

        return $query->orderBy(Document::RECEIVING_OR_ACTIVATION_DATE, 'desc')->get();
    }

    public function groupDocuments($docs, $groupBy)
    {
        if ($groupBy == 'dewey') {
            $docs = $this->groupByDewey($docs);
        } else if ($groupBy == 'week') {
            $docs = $this->groupByWeek($docs);
        } else if ($groupBy == 'month') {
            $docs = $this->groupByMonth($docs);
        } else {
            $docs = ['docs' => [null => $docs], 'groups' => []];
        }

        return [$docs['docs'], $docs['groups']];
    }

    public function groupByWeek($docs)
    {
        $out = [
            'groups' => [],
            'docs' => [],
        ];

        foreach ($docs as $doc) {
            $year = $doc->{Document::RECEIVING_OR_ACTIVATION_DATE}->format('Y');
            $week = $doc->{Document::RECEIVING_OR_ACTIVATION_DATE}->format('W');

            $url = action('ReportsController@byWeek', ['report' => $this->id, 'week' => $year . '-' . $week]);
            $title = 'Uke ' . $week;

            $out['docs'][$title][] = $doc;
            $out['groups'][$title] = $url;
        }

        return $out;
    }

    public function groupByMonth($docs)
    {
        $out = [
            'groups' => [],
            'docs' => [],
        ];

        foreach ($docs as $doc) {
            $dt = $doc->{Document::RECEIVING_OR_ACTIVATION_DATE};
            $year = $dt->format('Y');
            $month = $dt->format('m');

            $url = action('ReportsController@byMonth', ['report' => $this->id, 'month' => $year . '-' . $month]);
            $title = $dt->formatLocalized('%B');

            $out['docs'][$title][] = $doc;
            $out['groups'][$title] = $url;
        }

        return $out;
    }

    public function groupByDewey($docs)
    {
        $out = [
            'groups' => [],
            'docs' => [],
        ];

        foreach ($docs as $doc) {
            if (!$doc->dewey_classification) {
                $title = '1000 Not yet assigned';
                // continue
            } else {
                $title = substr($doc->dewey_classification, 0, 2) . '0';
            }
            $out['docs'][$title][] = $doc;
        }

        ksort($out['docs']);

        // sort trick
        if (isset($out['docs']['1000 Not yet assigned'])) {
            $out['docs']['(Not yet assigned)'] = $out['docs']['1000 Not yet assigned'];
            unset($out['docs']['1000 Not yet assigned']);
        }

        return $out;
    }

    public function getLinkAttribute()
    {
        return action('ReportsController@show', ['id' => $this->id]);
    }
}
