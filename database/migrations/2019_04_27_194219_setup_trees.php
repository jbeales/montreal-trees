<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SetupTrees extends Migration
{
     /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trees', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('INV_TYPE');
            $table->unsignedBigInteger('EMP_NO');
            $table->unsignedTinyInteger('ARROND');
            $table->string('ARROND_NOM');
            $table->string('Rue')->nullable();
            $table->string('COTE')->nullable();
            $table->string('No_civique')->nullable();
            $table->string('Emplacement')->nullable();
            $table->float('Coord_X', 15, 4)->nullable(); // 5043932.522
            $table->float('Coord_Y', 15, 4)->nullable();
            $table->string('SIGLE')->nullable();
            $table->string('Essence_latin')->nullable();
            $table->string('Essence_fr')->nullable();
            $table->string('ESSENCE_ANG')->nullable();
            $table->float('DHP')->nullable();
            $table->datetime('Date_releve')->nullable();
            $table->datetime('Date_plantation')->nullable();
            $table->string('localisation')->nullable();
            $table->string('CODE_PARC')->nullable();
            $table->string('NOM_PARC')->nullable();
            $table->float('Longitude', 9, 6)->nullable();
            $table->float('Latitude', 9, 6)->nullable();
            $table->point('location')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trees');
    }
}
