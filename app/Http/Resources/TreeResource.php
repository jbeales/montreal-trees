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
                'id' => $this->id,
                'emp' => $this->EMP_NO,
                'latin' => $this->Essence_latin,
                'fr' => $this->Essence_fr,
                'ang' => $this->ESSENCE_ANG,
                'updated' => $this->Date_releve,
                'planted' => $this->Date_plantation,
                'diameter' => $this->DHP,
            ]
        ];
    }
}
