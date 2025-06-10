<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TreeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'type' => 'Feature',
            'geometry' => [
                'type' => 'Point',
                'coordinates' => [$this->Longitude, $this->Latitude]
            ],
            'properties' => [
                'id' => $this->INV_TYPE . $this->EMP_NO,
                'emp' => $this->EMP_NO,
                'latin' => $this->Essence_latin,
                'fr' => $this->Essence_fr,
                'fr_clean' => \remove_accents($this->Essence_fr),
                'ang' => $this->ESSENCE_ANG,
                'updated' => empty($this->Date_releve) ? 'inconnu' : $this->Date_releve,
                'planted' => empty($this->Date_plantation) ? 'inconnu' : $this->Date_plantation,
                'diameter' => $this->DHP,
            ]
        ];
    }
}
