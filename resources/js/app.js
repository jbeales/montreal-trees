
(function() {

	var previousBounds,
		hasSomeTrees = false;

	window.mapboxgl.accessToken = '';

	var map = new mapboxgl.Map({
		container: document.querySelector('.map'), // container id
		style: 'mapbox://styles/mapbox/satellite-streets-v9',
		center: [ -73.567229, 45.453523 ], // starting position [lng, lat]
		zoom: 15 // starting zoom
	});

	function getAreaFromBounds(bounds) {
		return Math.abs(bounds.getNorth() - bounds.getSouth()) * Math.abs(bounds.getEast() - bounds.getWest());
	}

	/**
	 * Returns the percentage difference between newArea and oldAre
	 * @param  {float} newArea  The new area
	 * @param  {float} oldArea	The old area
	 * @return {float}          The difference, as a percentage.
	 */
	function calculateAreaDiff( newBounds, oldBounds ) {

		var newArea = getAreaFromBounds(newBounds),
			oldArea = getAreaFromBounds(oldBounds);

		return 100 * ( (newArea - oldArea) / oldArea );
	}

	function calculateCenterDiff( newBounds, oldBounds ) {

		var vertDist = Math.abs(newBounds.getCenter().lat - oldBounds.getCenter().lat),
			horizontalDist = Math.abs(newBounds.getCenter().lng - oldBounds.getCenter().lng),
			oldWidth = Math.abs(oldBounds.getEast() - oldBounds.getWest()),
			oldHeight = Math.abs(oldBounds.getNorth() - oldBounds.getSouth()),
			vertDiff = (vertDist / oldHeight) * 100,
			horizontalDiff = (horizontalDist / oldWidth) * 100;

		return ( vertDiff > horizontalDiff ) ? vertDiff : horizontalDiff;
	}

	// Add zoom and rotation controls to the map.
	map.addControl(new mapboxgl.NavigationControl());
	map.on('idle', function(evt) {
		var bounds = evt.target.getBounds();

		if( typeof previousBounds === 'undefined') {
			previousBounds = bounds;
		}

		console.log(calculateAreaDiff(bounds, previousBounds));

		// if area is 10% different from currentArea
		// Or the center has moved 20% of the previous bounding box 
		if( 
			(
				calculateAreaDiff(bounds, previousBounds) > 10 ||
				calculateCenterDiff(bounds, previousBounds) > 20 ||
				!hasSomeTrees
			) &&
			evt.target.getZoom() > 16
		) {
			previousBounds = bounds;
			hasSomeTrees = true;
			getTreesForBounds(bounds);
		}

	});



	function getTreesForBounds(bounds) {

		if(map.getLayer('trees')) {
			map.removeLayer('trees');
		}

		if(map.getSource('tree-json')) {
			map.removeSource('tree-json');
		}
	
		map.addSource('tree-json', {
		   	type: 'geojson',
		   	data: "/api/trees?bounds=" + JSON.stringify(bounds.toArray())
		   });
		 
		map.addLayer({
			"id": "trees",
			"type": "circle",
			"source": "tree-json",
			"paint": {
				"circle-radius": 5,
				"circle-color": "#3887be"
			}
		});

		// Change the cursor to a pointer when the mouse is over the states layer.
		map.on('mouseenter', 'trees', function () {
			map.getCanvas().style.cursor = 'pointer';
		});
 
		// Change it back to a pointer when it leaves.
		map.on('mouseleave', 'trees', function () {
			map.getCanvas().style.cursor = '';
		});



		// When a click event occurs on a feature in the states layer, open a popup at the
		// location of the click, with description HTML from its properties.
		map.on('click', 'trees', function (e) {
			new mapboxgl.Popup()
			.setLngLat(e.lngLat)
			.setHTML(makeTreeDesc(e.features[0].properties))
			.addTo(map);
		});
		 

		function makeTreeDesc(props) {
			$html = '<article class="tree-details">';
			$html += '<h4>' + props.latin + '</h4>';
			$html += '<p><strong>FR:</strong> ' + props.fr + '</p>';
			$html += '<p><strong>EN:</strong> ' + props.ang + '</p>';
			if(props.planted.length > 0 && props.planted !== null) {
				$html += '<p><strong>Planté:</strong> ' + props.planted + '</p>';
			}
			$html += '</article>';

			return $html;

		}
		
	}


})();




