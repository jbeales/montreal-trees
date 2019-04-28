
(function() {

	var previousBounds,
		hasSomeTrees = false,
		map,
		circles = [],
		infowindows = [],
		loader;


    window.initMap = function() {
        map = new google.maps.Map(document.querySelector('.map'), {
          center: {lat: 45.453523, lng: -73.567229},
          zoom: 12,
          mapTypeId: 'hybrid'

        });

        map.addListener('bounds_changed', function() {
        	var bounds = map.getBounds();
        	

			if( typeof previousBounds === 'undefined') {
				previousBounds = bounds;
			}

			
			// if area is 10% different from currentArea
			// Or the center has moved 20% of the previous bounding box 
			if( 
				(
					calculateAreaDiff(bounds, previousBounds) > 10 ||
					calculateCenterDiff(bounds, previousBounds) > 10 ||
					!hasSomeTrees
				) &&
				map.getZoom() > 15
			) {
				previousBounds = bounds;
				hasSomeTrees = true;
				getTreesForBounds(bounds);
			}
        });

        loader = document.querySelector('.loading');

    };




	function getAreaFromBounds(bounds) {
		return Math.abs(bounds.getNorthEast().lng() - bounds.getSouthWest().lng()) * Math.abs(bounds.getNorthEast().lat() - bounds.getSouthWest().lat());
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

		var vertDist = Math.abs(newBounds.getCenter().lat() - oldBounds.getCenter().lat()),
			horizontalDist = Math.abs(newBounds.getCenter().lng() - oldBounds.getCenter().lng()),
			oldWidth = Math.abs(oldBounds.getNorthEast().lng() - oldBounds.getSouthWest().lng()),
			oldHeight = Math.abs(oldBounds.getNorthEast().lat() - oldBounds.getSouthWest().lng()),
			vertDiff = (vertDist / oldHeight) * 100,
			horizontalDiff = (horizontalDist / oldWidth) * 100;

		return ( vertDiff > horizontalDiff ) ? vertDiff : horizontalDiff;
	}




	function getTreesForBounds(bounds) {
		startLoading();
		var oReq = new XMLHttpRequest();
		oReq.addEventListener("load", reqListener);
		oReq.open("GET", "/api/trees?bounds=" + JSON.stringify(bounds.toJSON()));
		oReq.send();	
	}

	function reqListener () {

		var geojson = JSON.parse(this.responseText);
		geojson.features.forEach(function(feature) {

			if( !circles[feature.properties.id]) {
				var center = new google.maps.LatLng(feature.geometry.coordinates[1],feature.geometry.coordinates[0]),
					treeCircle = new google.maps.Circle({
			            strokeColor: '#00FF00',
			            strokeOpacity: 1,
			            strokeWeight: 2,
			            fillColor: '#00FF00',
			            fillOpacity: 0.35,
			            map: map,
			            center: center,
			            radius: 1.25
			        });
			        circles[feature.properties.id] = treeCircle;
			        

		        treeCircle.addListener('click', function() {
		        	infowindow = new google.maps.InfoWindow({
			          content: makeTreeDesc(feature.properties)
			        });
		          	infowindow.open(map);
		          	infowindow.setPosition(treeCircle.center);
		          	infowindows.push(infowindow);
		        });
		    }
		});

		stopLoading();
	}



	function makeTreeDesc(props) {
		$html = '<article class="tree-details">';
		$html += '<h4>' + props.latin + '</h4>';
		$html += '<p><strong>FR:</strong> ' + props.fr + '</p>';
		$html += '<p><strong>EN:</strong> ' + props.ang + '</p>';
		if(props.diameter !== null) {
			$html += '<p><strong>Diameter:</strong> ' + props.diameter + '</p>';	
		}
		if(props.planted !== null) {
			$html += '<p><strong>Planté:</strong> ' + props.planted + '</p>';
		}
		$html += '<p><strong>Updated:</strong> ' + props.updated + '</p>';
		$html += '</article>';

		return $html;
	}

	function startLoading() {
		loader.classList.add('active');
	}

	function stopLoading() {
		loader.classList.remove('active');
	}


})();




