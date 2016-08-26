<?php

use App\Importers\AnalyticsReportImporter;
use Illuminate\Foundation\Testing\DatabaseTransactions;


class ImportPoLinesReportTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testImportPoLinesReport()
    {
        $data = file_get_contents(__DIR__ . '/data/pbooks.csv');

        $imported = AnalyticsReportImporter::importCsv($data);
        $this->assertEquals(104, $imported);

        $this->seeInDatabase('documents', [
            'mms_id' => '999919818003302204',
            'title' => 'Søndre Taasen hovedgaard : hus, gård og mennesker gjennom 200 år',
        ]);
    }
}
