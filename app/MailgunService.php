<?php

namespace App;

use App\Importers\AnalyticsReportImporter;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class MailgunService
{
    protected $http;
    protected $importers = [
        'Nyhetslister - po_lines' => AnalyticsReportImporter::class,
        'Nyhetslister - elektroniske bøker' => AnalyticsReportImporter::class,
        'Nyhetslister - trykte bøker' => AnalyticsReportImporter::class,
    ];

    public function __construct(Client $http)
    {
        $this->http = $http;
    }

    protected function getJson($url)
    {
        $response = $this->http->get($url);
        return json_decode($response->getBody());
    }

    protected function emailsBetween(Carbon $start, Carbon $end, Callable $cb)
    {
        Log::info('Checking for stored emails received between ' . $start->toDateString() . ' and ' . $end->toDateString());

        $response = $this->http->get('events', [
            'params' => [
                'event' => 'stored',
                'limit' => 300,
                'begin' => $start->timestamp,
                'end' => $end->timestamp,
                'ascending' => true,
            ]
        ]);
        $body = json_decode($response->getBody());

        print("Checking...");
        foreach ($body->items as $item) {
            print(".");
            $cb($item);
        }
    }

    public function fetchStoredAttachments($contentType = 'text/csv')
    {
        $lastEventTime = Status::find('last_event_time');
        $startTime = Carbon::now()->subDays(2);

        if (!is_null($lastEventTime)) {
            $startTime = Carbon::parse($lastEventTime->value);
        }

        # Stop 30 minutes ago   to be on the safe side, see https://documentation.mailgun.com/api-events.html#event-polling
        $endTime = Carbon::now()->subMinutes(30);

        if ($startTime > $endTime) {
            return;
        }

        $this->emailsBetween($startTime, $endTime, function($item) use ($contentType) {

            $subject = $item->message->headers->subject;

            $importer = array_get($this->importers, $subject);

            if (is_null($importer)) {
                return;
            }

            $email = $this->getJson($item->storage->url);
            $csvUrl = '';
            foreach ($email->attachments as $att) {
                if ($att->{'content-type'} == $contentType) {
                    $csvUrl = $att->url;
                }
            }
            if (!$csvUrl) {
                return;
            }


            $csv_data = $this->http->get($csvUrl)->getBody();

            file_put_contents(__DIR__ . '/report.csv', $csv_data);

            print(' (something to import) ');
            $importer::import($csv_data);
        });
    }
}
