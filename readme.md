# alma-newbooks-db

This is a tool for presenting data about recent acquisitions in Alma.
Data is harvested from Alma Analytics nightly and stored in a local database
to support queries we cannot do in Analytics directly.

## Setup

You'll need PHP 5.6.4+ and Composer, and a quite recent version of NodeJS.

    composer install
    npm install
    gulp

Setup DB configuration, etc. in the `.env` file. Then

    php artisan migrate

To enable automatic harvesting every night, add

    * * * * * php /path/to/artisan schedule:run >> /dev/null 2>&1

to your crontab. (This runs the artisan task scheduler every minute, but it
will exit right away if there's nothing to do).

## Development

To start a development server:

   php artisan serve

And optionally, if you work on js/css:

   gulp watch

## Analytics setup

You need to setup three reports described below. Unfortunately there's no way
to paste a query into Oracle BI EE, so you need to drag and drop all the columns
and hope that you don't make a mistake.

For all date fields, set date format ODBC: YYYY-MM-DD.

### 1) `new_physical`: Physical books received recently

List of physical items who are not deleted (lifecycle != "Deleted"),
and whose bibliographic record is not "supressed from discovery", sorted
and limited by `"Receiving   Date"` [sic].

We limit by material type to exclude e.g. "Journal", "Issue" and so on.
The exact list isn't set in stone. We include "None", since, for some reason,
we have quite a long list of items with this value.

```sql
SELECT
   0 s_0,
   "Physical Items"."Bibliographic Details"."Author" s_1,
   "Physical Items"."Bibliographic Details"."Bibliographic Level" s_2,
   "Physical Items"."Bibliographic Details"."Dewey Classification Top Line" s_3,
   "Physical Items"."Bibliographic Details"."Dewey Classification" s_4,
   "Physical Items"."Bibliographic Details"."Edition" s_5,
   "Physical Items"."Bibliographic Details"."MMS Id" s_6,
   "Physical Items"."Bibliographic Details"."Publication Date" s_7,
   "Physical Items"."Bibliographic Details"."Publication Place" s_8,
   "Physical Items"."Bibliographic Details"."Publisher" s_9,
   "Physical Items"."Bibliographic Details"."Series" s_10,
   "Physical Items"."Bibliographic Details"."Title" s_11,
   "Physical Items"."Fund Ledger"."Fund Ledger Code" s_12,
   "Physical Items"."Fund Ledger"."Fund Ledger Name" s_13,
   "Physical Items"."Fund Ledger"."Fund Type" s_14,
   "Physical Items"."Holding Details"."Holding Id" s_15,
   "Physical Items"."Holding Details"."Permanent Call Number" s_16,
   "Physical Items"."Location"."Library Name" s_17,
   "Physical Items"."Location"."Location Name" s_18,
   "Physical Items"."Physical Item Details"."Barcode" s_19,
   "Physical Items"."Physical Item Details"."Base Status" s_20,
   "Physical Items"."Physical Item Details"."Creation Date" s_21,
   "Physical Items"."Physical Item Details"."Creator" s_22,
   "Physical Items"."Physical Item Details"."Item Id" s_23,
   "Physical Items"."Physical Item Details"."Item Policy" s_24,
   "Physical Items"."Physical Item Details"."Material Type" s_25,
   "Physical Items"."Physical Item Details"."Process Type" s_26,
   "Physical Items"."Physical Item Details"."Receiving Date And Time" s_27,
   "Physical Items"."PO Line"."Acquisition Method" s_28,
   "Physical Items"."PO Line"."Additional Order Reference" s_29,
   "Physical Items"."PO Line"."Cancellation Reason" s_30,
   "Physical Items"."PO Line"."Order Line Type Code" s_31,
   "Physical Items"."PO Line"."Order Line Type" s_32,
   "Physical Items"."PO Line"."PO Creation Date" s_33,
   "Physical Items"."PO Line"."PO Line Creator" s_34,
   "Physical Items"."PO Line"."PO Line Modified By" s_35,
   "Physical Items"."PO Line"."PO Line Reference" s_36,
   "Physical Items"."PO Line"."PO Modification Date" s_37,
   "Physical Items"."PO Line"."Receiving Note" s_38,
   "Physical Items"."PO Line"."Receiving Status" s_39,
   "Physical Items"."PO Line"."Reporting Code" s_40,
   "Physical Items"."PO Line"."Sent Date" s_41,
   "Physical Items"."PO Line"."Source Type" s_42,
   "Physical Items"."PO Line"."Vendor Code" s_43,
   "Physical Items"."Temporary Location"."Library Name" s_44,
   "Physical Items"."Temporary Location"."Location Name" s_45,
   SUBSTRING("Physical Items"."Bibliographic Details"."ISBN" FROM 0 FOR POSITION(';' IN "Physical Items"."Bibliographic Details"."ISBN")-1) s_47
FROM "Physical Items"
WHERE
(("Bibliographic Details"."Suppressed From Discovery" = 'No') AND ("Physical Item Details"."Lifecycle" <> 'Deleted') AND ("PO Line"."PO Line Reference" LIKE 'POL-%') AND ("Physical Item Details"."Material Type" IN ('Audiobook', 'Blu-Ray', 'Blu-Ray And DVD', 'Book', 'DVD', 'None')))
ORDER BY 1, 30 DESC NULLS LAST, 2 ASC NULLS FIRST, 12 ASC NULLS FIRST, 10 ASC NULLS FIRST, 48 ASC NULLS FIRST, 5 ASC NULLS FIRST, 7 ASC NULLS FIRST, 6 ASC NULLS FIRST, 8 ASC NULLS FIRST, 4 ASC NULLS FIRST, 32 ASC NULLS FIRST, 40 ASC NULLS FIRST, 42 ASC NULLS FIRST, 43 ASC NULLS FIRST, 45 ASC NULLS FIRST, 29 ASC NULLS FIRST, 11 ASC NULLS FIRST, 3 ASC NULLS FIRST, 19 ASC NULLS FIRST, 17 ASC NULLS FIRST, 22 ASC NULLS FIRST, 23 ASC NULLS FIRST, 20 ASC NULLS FIRST, 24 ASC NULLS FIRST, 26 ASC NULLS FIRST, 31 ASC NULLS FIRST, 27 ASC NULLS FIRST, 25 ASC NULLS FIRST, 16 ASC NULLS FIRST, 28 ASC NULLS FIRST, 21 ASC NULLS FIRST, 9 ASC NULLS FIRST, 18 ASC NULLS FIRST, 44 ASC NULLS FIRST, 13 ASC NULLS FIRST, 34 ASC NULLS FIRST, 37 ASC NULLS FIRST, 33 ASC NULLS FIRST, 35 ASC NULLS FIRST, 41 ASC NULLS FIRST, 39 ASC NULLS FIRST, 46 ASC NULLS FIRST, 47 ASC NULLS FIRST, 14 ASC NULLS FIRST, 15 ASC NULLS FIRST, 36 ASC NULLS FIRST, 38 ASC NULLS FIRST
FETCH FIRST 500001 ROWS ONLY
```

Make sure not to include additional columns with names that clash with existing columns
(such as `"Physical Items"."Bibliographic Details"."Creation Date"`). In Oracle BI EE
we're not allowed to do `SELECT ... AS ...`, so in the resulting CSV file, we will get
two columns with the same name.

### 2) `new_electronic`: Electronic books activated recently

E-inventory having `"Material Type" = 'Book'` (to exclude journals, etc.),
sorted and limited by `"Portfolio Activation Date"`.

```sql
SELECT
   0 s_0,
   "E-Inventory"."Bibliographic Details"."Author" s_1,
   "E-Inventory"."Bibliographic Details"."Bibliographic Level" s_2,
   "E-Inventory"."Bibliographic Details"."Dewey Classification" s_3,
   "E-Inventory"."Bibliographic Details"."Edition" s_4,
   "E-Inventory"."Bibliographic Details"."ISBN" s_5,
   "E-Inventory"."Bibliographic Details"."MMS Id" s_6,
   "E-Inventory"."Bibliographic Details"."Publication Date" s_7,
   "E-Inventory"."Bibliographic Details"."Publisher" s_8,
   "E-Inventory"."Bibliographic Details"."Series" s_9,
   "E-Inventory"."Bibliographic Details"."Title" s_10,
   "E-Inventory"."Dewey Classifications"."Dewey Number" s_11,
   "E-Inventory"."Electronic Collection"."Public Name" s_12,
   "E-Inventory"."Institution"."Institution Name" s_13,
   "E-Inventory"."Portfolio Activation Date"."Portfolio Activation Date" s_14,
   "E-Inventory"."Portfolio Creation Date"."Portfolio Creation Date" s_15,
   "E-Inventory"."Portfolio Library Unit"."Library Code" s_16,
   "E-Inventory"."Portfolio Library Unit"."Library Name" s_17,
   "E-Inventory"."Portfolio PO Line"."PO Line Reference" s_18,
   "E-Inventory"."Portfolio PO Line"."Status" s_19,
   "E-Inventory"."Portfolio"."Availability" s_20,
   "E-Inventory"."Portfolio"."Creator" s_21,
   "E-Inventory"."Portfolio"."Is Free" s_22,
   "E-Inventory"."Portfolio"."Life Cycle" s_23,
   "E-Inventory"."Portfolio"."Material Type" s_24,
   "E-Inventory"."Portfolio"."Portfolio Id" s_25
FROM "E-Inventory"
WHERE
(("Portfolio"."Material Type" = 'Book') AND ("Bibliographic Details"."Suppressed From Discovery" = 'No'))
ORDER BY 1, 15 DESC NULLS LAST, 21 ASC NULLS FIRST, 22 ASC NULLS FIRST, 23 ASC NULLS FIRST, 19 ASC NULLS FIRST, 20 ASC NULLS FIRST, 14 ASC NULLS FIRST, 12 ASC NULLS FIRST, 16 ASC NULLS FIRST, 24 ASC NULLS FIRST, 25 ASC NULLS FIRST, 7 ASC NULLS FIRST, 11 ASC NULLS FIRST, 13 ASC NULLS FIRST, 26 ASC NULLS FIRST, 4 ASC NULLS FIRST, 5 ASC NULLS FIRST, 6 ASC NULLS FIRST, 9 ASC NULLS FIRST, 8 ASC NULLS FIRST, 10 ASC NULLS FIRST, 2 ASC NULLS FIRST, 3 ASC NULLS FIRST, 17 ASC NULLS FIRST, 18 ASC NULLS FIRST
FETCH FIRST 500001 ROWS ONLY
```

### 3) `new_po_lines`: PO lines modified recently (not in use atm)

PO lines having `"Reporting Code" IN ('ELECTRONICBOOK', 'PRINTBOOK'))` and
`"Fund Type" = 'Allocated fund'`, sorted and limited by modification date.

```sql
SELECT
   0 s_0,
   "Funds Expenditure"."Fund Ledger"."Fund Ledger Code" s_1,
   "Funds Expenditure"."Fund Ledger"."Fund Ledger Name" s_2,
   "Funds Expenditure"."Fund Ledger"."Fund Type" s_3,
   "Funds Expenditure"."PO Line"."Acquisition Method" s_4,
   "Funds Expenditure"."PO Line"."Additional Order Reference" s_5,
   "Funds Expenditure"."PO Line"."Cancellation Reason" s_6,
   "Funds Expenditure"."PO Line"."Order Line Type Code" s_7,
   "Funds Expenditure"."PO Line"."Order Line Type" s_8,
   "Funds Expenditure"."PO Line"."PO Creation Date" s_9,
   "Funds Expenditure"."PO Line"."PO Creator" s_10,
   "Funds Expenditure"."PO Line"."PO Line Creation Date" s_11,
   "Funds Expenditure"."PO Line"."PO Line Creator" s_12,
   "Funds Expenditure"."PO Line"."PO Line Modification Date" s_13,
   "Funds Expenditure"."PO Line"."PO Line Modified By" s_14,
   "Funds Expenditure"."PO Line"."PO Line Reference" s_15,
   "Funds Expenditure"."PO Line"."PO Number" s_16,
   "Funds Expenditure"."PO Line"."Receiving Note" s_17,
   "Funds Expenditure"."PO Line"."Receiving Status" s_18,
   "Funds Expenditure"."PO Line"."Reporting Code" s_19,
   "Funds Expenditure"."PO Line"."Sent Date" s_20,
   "Funds Expenditure"."PO Line"."Source Type" s_21,
   "Funds Expenditure"."PO Line"."Status" s_22,
   "Funds Expenditure"."PO Line"."Vendor Code" s_23
FROM "Funds Expenditure"
WHERE
(("PO Line"."PO Line Modification Date" >= TIMESTAMPADD(SQL_TSI_DAY, -10, CURRENT_DATE)) AND ("PO Line"."Reporting Code" IN ('ELECTRONICBOOK', 'PRINTBOOK')) AND ("Fund Ledger"."Fund Type" = 'Allocated fund'))
ORDER BY 1, 12 DESC NULLS LAST, 6 ASC NULLS FIRST, 11 ASC NULLS FIRST, 10 ASC NULLS FIRST, 13 ASC NULLS FIRST, 18 ASC NULLS FIRST, 19 ASC NULLS FIRST, 21 ASC NULLS FIRST, 22 ASC NULLS FIRST, 23 ASC NULLS FIRST, 24 ASC NULLS FIRST, 9 ASC NULLS FIRST, 8 ASC NULLS FIRST, 5 ASC NULLS FIRST, 7 ASC NULLS FIRST, 14 ASC NULLS FIRST, 15 ASC NULLS FIRST, 17 ASC NULLS FIRST, 20 ASC NULLS FIRST, 16 ASC NULLS FIRST, 2 ASC NULLS FIRST, 3 ASC NULLS FIRST, 4 ASC NULLS FIRST
FETCH FIRST 500001 ROWS ONLY
```

## Queue driver

By default harvest jobs are carried out synchronously (`QUEUE_DRIVER=sync` in `.env`).
If you're in the mood for something else,
you can set `QUEUE_DRIVER=database` and fire up a background worker process that
processes the queue. The main benefit is that failed jobs are retried
automatically. The main drawback is that you must ensure the worker process keeps
running, so you need to configure something like supervisor. See the
[Laravel docs](https://laravel.com/docs/5.3/queues). Here's an example
`ub-tilvekst-worker.ini` file:

```
[program:ub-tilvekst-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/almanewbooks/artisan queue:work database --sleep=3 --tries=3
autostart=true
autorestart=true
user=apache
numprocs=1
redirect_stderr=true
stdout_logfile=/path/to/almanewbooks/storage/logs/worker.log
```

After code changes:

    sudo supervisorctl restart ub-tilvekst-worker:*

## Some lessons learned

* Bibliographic data from the Analytics API cannot always be presented as-is.
  For instance,
  * The `title` fields contain `245 $a` and `$b` concatenated using space, giving
    titles like "Maxwell's Enduring Legacy A Scientific History of the Cavendish Laboratory"
    rather than "Maxwell's Enduring Legacy : A Scientific History of the Cavendish Laboratory"
    when ISBD markers are not hard coded in the record.
  * The `series` include ISBD from `830 $x` and volume from `$v`:
    "Publications (Manchester Centre for Anglo-Saxon studies) 1478-6710 13"
  * The `author` fields contain information from `$d` (ex.: `Flo, Olav 1922-1989`),
    `$e` (ex.: `Gubernatis, J. E., author.`) and `$q` (`Jacobs, Kurt author. (Kurt Aaron),`)
  * The ISBN field contains a semicolon-separated list of isbn numbers, with no indication what
    number belongs to the document at hand.
  * Editors?? Where are they? Not shown in Primo either 
    (https://bibsys-almaprimo.hosted.exlibrisgroup.com:443/UBO:default_scope:BIBSYS_ILS71535636290002201)
* Adding temporary location name to the selected columns led to exclusion of *some*
    documents without temporary location, but not all. Number of rows dropped
    from 1315 to 1298 rows. WHAT??
    * Solution: Seems like we actually need to loop over all items and query their status.
* Empty values are coded in different ways. Some are truly empty, others hold the value
  "Unknown" or "UNASSIGNED location" or "0-00-00 00:00:00" for dates. I try to map them all to null.
