<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class PopulateTrees extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

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

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DELETE FROM trees');
    }
}
