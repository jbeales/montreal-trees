<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UpdateTreeDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update-tree-data';


    protected $main_api_url = 'https://data.montreal.ca/api/3/action/datastore_search?resource_id=64e28fe6-ef37-437a-972d-d1d3f1f7d891&sort=_id%20asc';

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


        do {

        } while( !empty( $this->next_api_url));

       // API https://data.montreal.ca/api/3/action/datastore_search?resource_id=64e28fe6-ef37-437a-972d-d1d3f1f7d891&sort=_id%20asc








        $timezone = new DateTimeZone('America/Toronto');
                $created = 0;
                if (($handle = fopen ( storage_path('app/rawdata/arbres-publics.csv'), 'r' )) !== FALSE) {
                    while ( ($data = fgetcsv ( $handle, 0, ',' )) !== FALSE ) {

                        if($created === 0) {
                            $created++;
                            continue;
                        }

                        if(!empty( $data[20])) {



                            $releve = null;
                            if( !empty($data[15])) {
                                $releve = Carbon\Carbon::createFromFormat('Y-m-d\TH:i:s', $data[15], $timezone);
                            }
                            $plantation = null;
                            if( !empty($data[16])) {
                                $plantation = Carbon\Carbon::createFromFormat('Y-m-d\TH:i:s', $data[16], $timezone);
                            }

                            $dhp = 0;
                            if(!empty($data[14])) {
                                $dhp = $data[14];
                            }


                            App\Tree::create([
                                'INV_TYPE' => $data[0],
                                'EMP_NO' => $data[1],
                                'ARROND' => $data[2],
                                'ARROND_NOM' => $data[3],
                                'Rue' => $data[4],
                                'COTE' => $data[5],
                                'No_civique' => $data[6],
                                'Emplacement' => $data[7],
                                'Coord_X' => $data[8],
                                'Coord_Y' => $data[9],
                                'SIGLE' => $data[10],
                                'Essence_latin' => $data[11],
                                'Essence_fr' => $data[12],
                                'ESSENCE_ANG' => $data[13],
                                'DHP' => $dhp,
                                'Date_releve' => $releve,
                                'Date_plantation' => $plantation,
                                'localisation' => $data[17],
                                'CODE_PARC' => $data[18],
                                'NOM_PARC' => $data[19],
                                'Longitude' => $data[20],
                                'Latitude' => $data[21],
                                'location' => DB::raw(sprintf("POINT(%s, %s)", floatval($data[20]), floatval($data[21])))
                            ]);

                            $created++;
                        }
                    }
                    fclose ( $handle );
                }

                echo "\nThere were $created trees added to the DB.\n";

                Schema::table('trees', function (Blueprint $table) {
                    $table->spatialIndex('location');
                });
    }


    protected function import_api_page() {

    }

    protected function fetch_api_page() {
        if(!empty($this->next_api_url)) {
            $url = $this->next_api_url;
        } else {
            $url = $this->main_api_url;
        }


    }
}

