<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tree extends Model
{
    public $timestamps = false;

    protected $dates = [
    	'Date_releve',
    	'Date_plantation'
    ];


    protected $guarded = [];
/*
    #[Scope]
    public function withinBounds(Builder $query, $topLeft, $bottomRight)
    {
        $query->whereRaw(
            'ST_Within(location, ST_GeometryFromText(?, 4326))',
            'POLYGON'
            [
                $topLeft[0], // west
                $topLeft[1], // north
                $bottomRight[0], // east
                $bottomRight[1] // south
            ]
        );
    }
    {

*/


}
