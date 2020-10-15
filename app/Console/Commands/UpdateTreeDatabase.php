<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class UpdateTreeDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update-tree-data';


//    protected $main_api_url = 'https://data.montreal.ca/api/3/action/datastore_search?resource_id=64e28fe6-ef37-437a-972d-d1d3f1f7d891&sort=_id%20asc';

    protected $next_api_url;

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

        // url
        $url = 'https://montreal.l3.ckan.io/dataset/b89fd27d-4b49-461b-8e54-fa2b34a628c4/resource/64e28fe6-ef37-437a-972d-d1d3f1f7d891/download/arbres-publics.csv';

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

        $this->info('Preparing.');
        DB::statement('DROP TABLE IF EXISTS trees_new');
        DB::statement('CREATE TABLE trees_new LIKE trees');
        $this->info('Loading Data.');
        DB::getPdo()->exec(
            "LOAD DATA LOCAL INFILE " . DB::getPdo()->quote(Storage::path($treefilename)) . " INTO TABLE trees_new
            FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"'
            IGNORE 1 LINES
            (
                INV_TYPE,
                EMP_NO,
                ARROND,
                ARROND_NOM,
                Rue,
                COTE,
                No_civique,
                Emplacement,
                Coord_X,
                Coord_Y,
                SIGLE,
                Essence_latin,
                Essence_fr,
                ESSENCE_ANG,
                DHP,
                Date_releve,
                Date_plantation,
                localisation,
                CODE_PARC,
                NOM_PARC,
                Longitude,
                Latitude
            )"
        );

        $this->info('Setting Location column.');
        DB::statement("UPDATE trees_new SET location=POINT(Latitude, Longitude)");

        DB::statement('DROP TABLE IF EXISTS trees_old');
        DB::statement('RENAME TABLE trees TO trees_old');
        DB::statement('RENAME TABLE trees_new TO trees');

        $this->info('Done.');

    }
}

