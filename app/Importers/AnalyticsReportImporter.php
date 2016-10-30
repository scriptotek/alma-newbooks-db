<?php

namespace App\Importers;

use App\Document;
use App\Exceptions\InvalidRowException;
use League\Csv\Reader;
use voku\helper\UTF8;

class AnalyticsReportImporter
{
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
            if (preg_match('/^\d{4}-\d{2}-\d{2}T/', $row[$k])) {
                $row[$k] = str_replace('T', ' ', $row[$k]);
            }

            if ($row[$k] == '0000-00-00 00:00:00') {
                $row[$k] = null;
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

    public static function getKey($row)
    {
        $keys = [
            Document::ITEM_ID,      // Physical item report
            Document::PORTFOLIO_ID, // Electronic portfolio report
            Document::MMS_ID,       // Bibliographic record report
        ];

        foreach ($keys as $k) {
            if (isset($row[$k])) {
                return [$k => $row[$k]];
            }
        }
    }

    public static function importRowFromApi($row, $create = false)
    {
        try {
            $row = self::cleanRow($row);
        } catch (InvalidRowException $e) {
            // ignore this row
            return null;
        }

        $key = self::getKey($row);
        if (!$key) {
            throw new \RuntimeError('No key column found for row: ' . json_encode($row));
        }
        $doc = Document::firstOrNew($key);
        if (is_null($doc->id) && !$create) {
            return null;
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

        return $doc;
    }
}
