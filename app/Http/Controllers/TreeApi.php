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
			
			$points = [
				[$bounds->west, $bounds->north],
				[$bounds->east, $bounds->north],
				[$bounds->east, $bounds->south],
				[$bounds->west, $bounds->south],
			];

		} else {
			// [lng,lat]
			$southwest = $bounds[0];
			$northeast = $bounds[1];

			$points = [
				[$northeast[0], $southwest[1]],
				[$northeast[0], $northeast[1]],
				[$southwest[0], $northeast[1]],
				[$southwest[0], $southwest[1]],
			];
		}

		$substitutions = [];
		foreach( $points as $point ) {
			$substitutions[] = floatval($point[0]);
			$substitutions[] = floatval($point[1]);
		}

		// Add the first point as the last to close the polygon.
		$substitutions[] = floatval($points[0][0]);
		$substitutions[] = floatval($points[0][1]);

		$geomtext = sprintf(
			'POLYGON((%F %F, %F %F, %F %F, %F %F, %F %F))',
			...$substitutions
		);

		$trees = Tree::whereRaw(sprintf("ST_Within(location, ST_GeomFromText('%s'))", $geomtext))->get();

		return new TreeResourceCollection($trees);

	}

}
