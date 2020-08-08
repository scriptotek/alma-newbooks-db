<?php

namespace App\Importers;

use App\Document;
use App\Exceptions\InvalidRowException;
use Scriptotek\Alma\Analytics\Report;
use function Functional\reduce_left;

class AnalyticsReportImporter
{
    protected static function validateRow($row, $reportShortName)
    {
        $key = reduce_left(self::getKey($row), function($value, $index, $collection, $reduction) {
            return $index . '=' . $value;
        });
        $intro = "[$reportShortName] $key : ";

        if (isset($row[Document::MMS_ID]) && strlen($row[Document::MMS_ID]) != 18) {
            \Log::warning("$intro Expected MMS ID of length 18, but got " . strlen($row[Document::MMS_ID]) . ": '" . $row[Document::MMS_ID] . "'");
            throw new InvalidRowException();
        }

        if (isset($row['bib_modification_date']) && !preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}/', $row['bib_modification_date'])) {
            \Log::warning("$intro Invalid bib_modification_date: '" . $row['bib_modification_date'] . "'");
            throw new InvalidRowException();
        }

        /*
        if (isset($row[Document::PO_ID]) && !preg_match('/POL-/', $row[Document::PO_ID])) {
            \Log::warning("$intro Expected PO line reference to start with 'POL-', but got '" . $row[Document::PO_ID] . "'");
            throw new InvalidRowException();
        }*/
    }

    protected static function cleanRow($row, $reportShortName)
    {
        // Publication year: "[2012]" -> "2012", "c2012" -> "2012", etc.
        if (isset($row['publication_date'])) {
            if (preg_match('/[0-9]{4}/', $row['publication_date'], $matches)) {
                $row['publication_date'] = intval($matches[0]);
            } else {
                \Log::warning("Ignoring invalid publication date '" . $row['publication_date'] . "' for '" . $row[Document::PO_ID] . "'");
                $row['publication_date'] = null;
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
            Document::PO_ID,        // PO lines report
            Document::MMS_ID,       // Bibliographic record report
        ];

        foreach ($keys as $k) {
            if (isset($row[$k])) {
                return [$k => $row[$k]];
            }
        }
    }

    public static function docFromRow($row, $create = false, &$keyCache, Report $report)
    {
        $shortName = basename($report->path);

        try {
            self::validateRow($row, $shortName);
            $row = self::cleanRow($row, $shortName);
        } catch (InvalidRowException $e) {
            // ignore this row
            return null;
        }

        $key = self::getKey($row);
        if (!$key) {
            throw new \RuntimeError('No key column found for row: ' . json_encode($row));
        }
        $key_val = array_values($key)[0];
        if (in_array($key_val, $keyCache)) {
            // IGNORE DUPLICATE ROW
            // This happens for items with more than one fund ledger.
            return;
        }

        $keyCache[] = $key_val;

        $doc = Document::firstOrNew($key);
        if (is_null($doc->id) && !$create) {
            return null;
        }
        foreach ($row as $k => $v) {
            if (!in_array($k, Document::getFields(true))) {
                \Log::warning("[$shortName] Ignoring unknown field: $k");
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

        // Set item/portfolio id and creation_date
        if (isset($row['portfolio_id'])) {
            $doc->item_or_portfolio_id = $row['portfolio_id'];
        } elseif (isset($row['item_id'])) {
            $doc->item_or_portfolio_id = $row['item_id'];
        }
        if (isset($row['portfolio_creation_date'])) {
            $doc->item_or_portfolio_creation_date = $row['portfolio_creation_date'];
        } elseif (isset($row['item_creation_date'])) {
            $doc->item_or_portfolio_creation_date = $row['item_creation_date'];
        }

        return $doc;
    }
}
