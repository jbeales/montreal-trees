<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader;

class UpdateTreeDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update-tree-data {csvUrl?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates to the latest tree data from the City of Montreal\'s Open Data portal';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $url = $this->argument('csvUrl');

        if(empty($url)) {
            $url = 'https://donnees.montreal.ca/dataset/b89fd27d-4b49-461b-8e54-fa2b34a628c4/resource/64e28fe6-ef37-437a-972d-d1d3f1f7d891/download/arbres-publics.csv';
        }


        $treefilename = 'arbres-publics.csv';

        // Delete old CSV if it exists.
        if( Storage::exists($treefilename) ) {
            Storage::delete($treefilename);
        }

        $response = Http::withOptions([
            'sink' => Storage::path($treefilename),
        ])->get($url);

        if($response->successful()) {
            $this->info("Got CSV file");
        } else {
            $this->info("There was a problem receiving the CSV.");
            $this->info(print_r($response, true));
            return 0;
        }

        $csv = Reader::createFromPath(Storage::path($treefilename), 'r');

        $csv->setHeaderOffset(0); // Set the header offset to 0 to use the first row as headers
        foreach($csv->getRecords() as $record) {
            // Check if the record has the required fields
            if ( !empty($record['Longitude']) && !empty($record['Latitude'])) {

                DB::table('trees')->upsert(
                  [
                    'INV_TYPE' => $record['INV_TYPE'],
                    'EMP_NO' => $record['EMP_NO'],
                    'ARROND' => $record['ARROND'],
                    'ARROND_NOM' => $record['ARROND_NOM'],
                    'Rue' => $record['Rue'] ?? null,
                    'COTE' => $record['COTE'] ?? null,
                    'No_civique' => $record['No_civique'] ?? null,
                    'Emplacement' => $record['Emplacement'] ?? null,
                    'SIGLE' => $record['SIGLE'] ?? null,
                    'Essence_latin' => $record['Essence_latin'] ?? null,
                    'Essence_fr' => $record['Essence_fr'] ?? null,
                    'ESSENCE_ANG' => $record['ESSENCE_ANG'] ?? null,
                    'DHP' => !empty($record['DHP']) ? $record['DHP'] : null,
                    'Date_releve' => ($record['Date_Releve'] && $record['Date_Releve'] !== '0000-00-00 00:00:00') ? $record['Date_Releve'] : null,
                    'Date_plantation' => ($record['Date_Plantation'] && $record['Date_Plantation'] !== '0000-00-00 00:00:00') ? $record['Date_Plantation'] : null,
                    'localisation' => $record['LOCALISATION'] ?? null,
                    'CODE_PARC' => $record['CODE_PARC'] ?? null,
                    'NOM_PARC' => $record['NOM_PARC'] ?? null,
                    'Longitude' => floatval($record['Longitude']),
                    'Latitude' => floatval($record['Latitude']),
                    // Create a POINT from Longitude and Latitude
                    'location' => DB::raw("ST_PointFromText('POINT({$record['Latitude']} {$record['Longitude']})', 4326)"),
                  ],
                  ['INV_TYPE', 'EMP_NO'],
                  ['Rue', 'COTE', 'No_civique', 'Emplacement', 'SIGLE', 'Essence_latin', 'Essence_fr', 'ESSENCE_ANG', 'DHP', 'Date_releve', 'Date_plantation', 'localisation', 'CODE_PARC', 'NOM_PARC', 'Longitude', 'Latitude', 'location']
                );
            } else {
                $this->error('Missing Longitude or Latitude in record: ' . json_encode($record));
            }
        }
        $this->info('Done.');

        return 0;
    }
}

