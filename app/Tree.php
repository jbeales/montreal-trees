<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tree extends Model
{
    public $timestamps = false;

    protected $dates = [
    	'Date_releve',
    	'Date_plantation'
    ];


    protected $guarded = [];
}
