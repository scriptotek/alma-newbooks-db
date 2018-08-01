<?php

namespace App;

use Carbon\Carbon;

class DocumentBuilder extends \Illuminate\Database\Eloquent\Builder
{

    public function withinDateRange($startDate=null, $endDate=null, $field=Document::RECEIVING_OR_ACTIVATION_DATE)
    {
        $this->whereNotNull($field);

        if (!is_null($startDate)) {
            $this->where($field, '>=', $startDate->toDateString());
        }
        if (!is_null($endDate)) {
            $this->where($field, '<', $endDate->toDateString());
        }

        return $this;
    }

    public function fromMonth($year, $month)
    {
        $startDate = Carbon::create($year, $month, 1);
        $endDate = $startDate->copy()->addMonth();

        return $this->withinDateRange($startDate, $endDate)
            ->orderBy('title', 'asc');
    }

    public function fromWeek($year, $week)
    {
        $startDate = new Carbon(sprintf('%04dW%02d', $year, $week));
        $endDate = $startDate->copy()->addWeek();

        return $this->withinDateRange($startDate, $endDate)
            ->orderBy('title', 'asc');
    }

    public function nonReceived()
    {
        return $this->whereNull(Document::RECEIVING_OR_ACTIVATION_DATE);
    }

    public function received()
    {
        return $this->whereNotNull(Document::RECEIVING_OR_ACTIVATION_DATE);
    }

    /**
     * Return list of documents with unique {$fieldName}. If multiple
     * instances use the same {$fieldName}, only the last is returned.
     *
     * @return Document[]
     */
    public function getUnique($options = [])
    {
        $docs = [];
        $fieldName = array_get($options, 'fieldName', Document::MMS_ID);

        $limit = array_get($options, 'limit') ?: $this->getQuery()->limit;

        $this->take($limit * 4);  // quick hack. Proper grouping is quite difficult here:
        // - `distinct on (mms_id) *` does not work since we sort on receving_date
        // - `group by mms_id` does not work for the same reason

        $n = 0;
        foreach ($this->get() as $doc) {
            if (!array_key_exists($doc->{$fieldName}, $docs)) {
                $docs[$doc->{$fieldName}] = $doc;
                $n++;
                if ($n >= $limit) break;
            }
        }

        return array_values($docs);
    }

    public function groupedByWeek($field=Document::RECEIVING_OR_ACTIVATION_DATE)
    {
        $docs = [];

        foreach ($this->orderBy($field, 'desc')->received()->getUnique() as $doc) {
            $year = $doc->{$field}->format('Y');
            $week = $doc->{$field}->format('W');
            $key = $year . '-' . $week;
            $docs[$key][] = $doc;
        }

        return $docs;
    }

    // Redefine orderBy to *replace* the current order, not add to the current sort keys.
    public function orderBy($column, $direction = 'asc')
    {
        // Clear sort order first
        $this->getQuery()->orders = [];

        // Set sort order
        $this->getQuery()->orderBy($column, $direction);

        return $this;
    }

    public function groupedByMonth($field=Document::RECEIVING_OR_ACTIVATION_DATE)
    {
        $docs = [];

        $this->whereNotNull($field);

        foreach ($this->orderBy($field, 'desc')->received()->getUnique() as $doc) {
            $dt = $doc->{$field};
            $year = $dt->format('Y');
            $month = $dt->format('m');
            $key = $year . '-' . $month;
            $docs[$key][] = $doc;
        }

        return $docs;
    }

    public function groupedByDewey()
    {
        $docs = [];

        foreach ($this->received()->getUnique() as $doc) {
            if (!$doc->dewey_classification) {
                $key = 1000;
            } else {
                $key = floatval(substr($doc->dewey_classification, 0, 2) . '0');
            }
            $docs[$key][] = $doc;
        }

        ksort($docs);

        return $docs;
    }

    public function getGrouped(Report $report, $groupBy)
    {
        $groups = [];

        if ($groupBy == 'dewey') {

            $docs = $this->groupedByDewey();

            foreach ($docs as $key => $values) {
                $groups[$key] = [
                    'link' => null,
                    'title' => sprintf('%03d', $key),
                ];
            }

            $groups[1000] = [
                'link' => null,
                'title' => 'Not yet classified',
            ];

        } else if ($groupBy == 'week') {

            $docs = $this->groupedByWeek();

            foreach ($docs as $key => $values) {
                list($year, $week) = explode('-', $key);
                $groups[$key] = [
                    'link' => action('ReportsController@byWeek', [
                        'report' => $report->id,
                        'week' => $year . '-' . $week,
                    ]),
                    'title' => $values[0]->{Document::RECEIVING_OR_ACTIVATION_DATE}->formatLocalized('Uke %W, %Y'),
                ];
            }

        } else if ($groupBy == 'month') {

            $docs = $this->groupedByMonth();

            foreach ($docs as $key => $values) {
                list($year, $month) = explode('-', $key);
                $groups[$key] = [
                    'link' => action('ReportsController@byMonth', [
                        'report' => $report->id,
                        'month' => $year . '-' . $month,
                    ]),
                    'title' => $values[0]->{Document::RECEIVING_OR_ACTIVATION_DATE}->formatLocalized('%B'),
                ];
            }

        } else {
            $docs = [null => $this->getUnique()];
        }

        return [$docs, $groups];
    }


    protected function addStatement($key, $rel, $val)
    {
        if ($val) {
            switch ($rel) {
                case 'be':
                    $this->where($key, 'ILIKE', $val . '%');
                    break;
                case 'co':
                    $this->where($key, 'ILIKE', '%' . $val . '%');
                    break;
                case 'nc':
                    $this->where($key, 'NOT ILIKE', '%' . $val . '%');
                    break;
                case 'eq':
                    $this->where($key, '=', $val);
                    break;
                case 'ne':
                    $this->where($key, '!=', $val);
                    break;
                case 'gt':
                    $this->where($key, '>', $val);
                    break;
                case 'lt':
                    $this->where($key, '<', $val);
                    break;
                case 'gte':
                    $this->where($key, '>=', $val);
                    break;
                case 'lte':
                    $this->where($key, '<=', $val);
                    break;
            }
        } else {
            switch ($rel) {
                case 'nu':
                    $this->whereNull($key);
                    break;
                case 'nn':
                    $this->whereNotNull($key);
                    break;
            }
        }
    }

    public function fromRequest($req)
    {
        $this->orderBy($req['sort'], $req['sortDir'])
             ->whereNotNull($req['sort']);

        foreach ($req['statements'] as $stmt) {
            $this->addStatement($stmt['key'], $stmt['rel'], $stmt['val']);
        }

        return $this;
    }
}
