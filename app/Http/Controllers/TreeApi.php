<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Tree;
use App\Http\Resources\TreeResource;
use App\Http\Resources\TreeResourceCollection;

class TreeApi extends Controller
{
    


	public function trees(Request $request) {
		if(!$request->has('bounds')) {
			abort(400, __('A bounds parameter is required.'));
		}

		$bounds = json_decode($request->get('bounds'));

		if( isset($bounds->north) ) {
			$trees = Tree::where([
				['Latitude', '<', $bounds->north],
				['Latitude', '>', $bounds->south],
				['Longitude', '<', $bounds->east],
				['Longitude', '>', $bounds->west]
			])->get();
		} else {
			// [lng,lat]
			$southwest = $bounds[0];
			$northeast = $bounds[1];

			$trees = Tree::where([
				['Latitude', '<', $northeast[1]],
				['Latitude', '>', $southwest[1]],
				['Longitude', '<', $northeast[0]],
				['Longitude', '>', $southwest[0]]
			])->get();

		}

		

		return new TreeResourceCollection($trees);

	}

}
