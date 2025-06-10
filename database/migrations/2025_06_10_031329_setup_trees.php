<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('trees', function (Blueprint $table) {

            $table->charset('utf8mb4');
            $table->collation('utf8mb4_unicode_ci');

            $table->string('INV_TYPE');
            $table->unsignedBigInteger('EMP_NO');
            $table->unsignedTinyInteger('ARROND');
            $table->string('ARROND_NOM');
            $table->string('Rue')->nullable();
            $table->string('COTE')->nullable();
            $table->string('No_civique')->nullable();
            $table->string('Emplacement')->nullable();
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
            $table->geography('location', subtype: 'point', srid: 4326);

            $table->primary(['INV_TYPE', 'EMP_NO']);
            $table->spatialIndex('location');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trees');
    }
};
