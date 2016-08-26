<?php

namespace App\Importers;

use App\Document;
use App\Exceptions\InvalidRowException;
use League\Csv\Reader;
use voku\helper\UTF8;

class AnalyticsReportImporter
{
    /*protected static $keyMap = [

        // PO Line
        'PO Line Creator' => 'po_creator',
        'PO Line Reference' => 'po_id',
        'Acquisition Method' => 'acquisition_method',
        'Reporting Code' => 'reporting_code',
        'Receiving Note' => 'receiving_note',
        'Additional Order Reference' => 'additional_reference',
        'Cancellation Reason' => 'cancellation_reason',
        'Vendor Code' => 'vendor_code',
        'Fund Ledger Code' => 'fund_code',

        'PO Creation Date' => 'po_creation_date',
        'PO Line Modification Date' => 'po_modification_date',
        'Sent Date' => 'sent_date',
        'Receiving   Date' => 'receiving_date',

        // Bibliographic
        'MMS Id' => 'mms_id',
        'Title' => 'title',
        'Edition' => 'edition',
        'Author' => 'author',
        'Publisher' => 'publisher',
        'Publication Date' => 'pub_year',
        'Publication Place' => 'pub_place',
        'Series' => 'series',
        'Bibliographic Level' => 'bib_level',
        'Dewey Classification' => 'ddc',
        'Dewey Classification Top Line' => 'ddc_topline',
        'ISBN' => 'isbn',

        // Physical holding
        'Library Name' => 'library_name',
        'Location Name' => 'location_name',
        'Permanent Call Number' => 'call_number',
        'Creation Date' => 'item_creation_date',
        'Creator' => 'item_creator',  // either item or portfolio really, since field is used in both reports

        // Electronic portfolio
        'Portfolio Id' => 'portfolio_id',
        'Public Name' => 'collection_name',
        'Portfolio Creation Date' => 'portfolio_creation_date',
        'Portfolio Activation Date' => 'activation_date'
    ];

    public static function importCsv($text)
    {
        $text = UTF8::toUTF8($text);
        $text = UTF8::cleanup($text);
        $csv = Reader::createFromString($text);
        $csv->setDelimiter("\t");

        $rows = $csv->fetchAssoc(0);
        $n = 0;
        foreach ($rows as $row) {
            $row = self::mapKeys($row);
            try {
                $row = self::cleanRow($row);
            } catch (InvalidRowException $e) {
                // ignore
                continue;
            }

            $doc = Document::firstOrNew([
                'po_id' => $row['po_id']
            ]);
            foreach ($row as $k => $v) {
                $doc->{$k} = $v;
            }

            if (isset($row['activation_date'])) {
                $doc->{Document::RECEIVING_OR_ACTIVATION_DATE} = $row['activation_date'];
            } elseif (isset($row['receiving_date'])) {
                $doc->{Document::RECEIVING_OR_ACTIVATION_DATE} = $row['receiving_date'];
            }

            if ($doc->save()) {
                $n++;
            }
        }
        return $n;
    }

    protected static function mapKeys($arr)
    {
        $out = [];
        foreach ($arr as $k => $v) {
            if (isset(static::$keyMap[$k]) && !empty($v)) {
                $out[static::$keyMap[$k]] = $v;
            }
        }
        return $out;
    }*/

    protected static function cleanRow($row)
    {
        // Publication year: "[2012]" -> "2012", "c2012" -> "2012", etc.
        if (isset($row['publication_date'])) {
            if (preg_match('/[0-9]{3,4}/', $row['publication_date'], $matches)) {
                $row['publication_date'] = $matches[0];
            }
        }

        // Remove some ISBD marks
        foreach (['title', 'publication_place', 'edition'] as $k) {
            if (isset($row[$k])) {
                $row[$k] = trim($row[$k], " \t\n\r\0\x0B/:;,.");
            }
        }

        // Make dates Carbon compatible
        foreach ($row as $k => $v) {
            if (preg_match('/^\d{4}-\d{2}-\d{2}T/', $v)) {
                $row[$k] = str_replace('T', ' ', $v);
            }
        }

        foreach ($row as $k => $v) {
            if ($v == '0-00-00 00:00:00') {
                // Some po lines rows, we're not really interested in these.
                throw new InvalidRowException();
            }
            if ($v == 'Unknown' or $v == 'UNASSIGNED location') {
                $row[$k] = null;
            }
        }

        if (isset($row[Document::PO_ID]) && !preg_match('/POL-/', $row[Document::PO_ID])) {
            \Log::warning('[AnalyticsReportImporter] Ignoring invalid PO line ref: ' . $row[Document::PO_ID]);
            throw new InvalidRowException();
        }

        // Remove year from author
       if (isset($row['author'])) {
           $row['author'] = preg_replace('/\s*[0-9]{4}-?([0-9]{4})?/', '', $row['author']);
           $row['author'] = str_replace(' author', '', $row['author']);
           $row['author'] = trim($row['author'], " \t\n\r\0\x0B.,");
       }

        return $row;
    }

    public static function importRowFromApi($row, $create = false)
    {
        try {
            $row = self::cleanRow($row);
        } catch (InvalidRowException $e) {
            // ignore this row
            return false;
        }

        $key = isset($row[Document::PO_ID]) ? [Document::PO_ID => $row[Document::PO_ID]] : [Document::MMS_ID => $row[Document::MMS_ID]];
        $doc = Document::firstOrNew($key);
        if (is_null($doc->id) && !$create) {
            return false;
        }
        foreach ($row as $k => $v) {
            if (!in_array($k, Document::getFields())) {
                \Log::warning('[AnalyticsReportImporter] Ignoring unknown field: ' . $k);
            } else {
                $doc->{$k} = $v;
            }
        }

        // Set sort date
        if (isset($row['activation_date'])) {
            $doc->{Document::RECEIVING_OR_ACTIVATION_DATE} = $row['activation_date'];
        } elseif (isset($row['receiving_date'])) {
            $doc->{Document::RECEIVING_OR_ACTIVATION_DATE} = $row['receiving_date'];
        }

        $doc->save();

        return true;
    }
}
