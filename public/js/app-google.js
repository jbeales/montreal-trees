/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "/";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 0);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/js/app-google.js":
/*!************************************!*\
  !*** ./resources/js/app-google.js ***!
  \************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function () {
  var previousBounds,
      hasSomeTrees = false,
      map,
      circles = [],
      infowindows = [],
      loader;

  window.initMap = function () {
    map = new google.maps.Map(document.querySelector('.map'), {
      center: {
        lat: 45.453523,
        lng: -73.567229
      },
      zoom: 12,
      mapTypeId: 'hybrid'
    });
    map.addListener('bounds_changed', function () {
      var bounds = map.getBounds();

      if (typeof previousBounds === 'undefined') {
        previousBounds = bounds;
      } // if area is 10% different from currentArea
      // Or the center has moved 20% of the previous bounding box 


      if ((calculateAreaDiff(bounds, previousBounds) > 10 || calculateCenterDiff(bounds, previousBounds) > 10 || !hasSomeTrees) && map.getZoom() > 15) {
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


  function calculateAreaDiff(newBounds, oldBounds) {
    var newArea = getAreaFromBounds(newBounds),
        oldArea = getAreaFromBounds(oldBounds);
    return 100 * ((newArea - oldArea) / oldArea);
  }

  function calculateCenterDiff(newBounds, oldBounds) {
    var vertDist = Math.abs(newBounds.getCenter().lat() - oldBounds.getCenter().lat()),
        horizontalDist = Math.abs(newBounds.getCenter().lng() - oldBounds.getCenter().lng()),
        oldWidth = Math.abs(oldBounds.getNorthEast().lng() - oldBounds.getSouthWest().lng()),
        oldHeight = Math.abs(oldBounds.getNorthEast().lat() - oldBounds.getSouthWest().lng()),
        vertDiff = vertDist / oldHeight * 100,
        horizontalDiff = horizontalDist / oldWidth * 100;
    return vertDiff > horizontalDiff ? vertDiff : horizontalDiff;
  }

  function getTreesForBounds(bounds) {
    startLoading();
    var oReq = new XMLHttpRequest();
    oReq.addEventListener("load", reqListener);
    oReq.open("GET", "/api/trees?bounds=" + JSON.stringify(bounds.toJSON()));
    oReq.send();
  }

  function reqListener() {
    var geojson = JSON.parse(this.responseText);
    geojson.features.forEach(function (feature) {
      if (!circles[feature.properties.id]) {
        var center = new google.maps.LatLng(feature.geometry.coordinates[1], feature.geometry.coordinates[0]),
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
        treeCircle.addListener('click', function () {
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

    if (props.diameter !== null) {
      $html += '<p><strong>Diameter:</strong> ' + props.diameter + '</p>';
    }

    if (props.planted !== null) {
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

/***/ }),

/***/ "./resources/sass/app.scss":
/*!*********************************!*\
  !*** ./resources/sass/app.scss ***!
  \*********************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ 0:
/*!********************************************************************!*\
  !*** multi ./resources/js/app-google.js ./resources/sass/app.scss ***!
  \********************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(/*! /Users/John/Work/arbres/arbres-montreal/resources/js/app-google.js */"./resources/js/app-google.js");
module.exports = __webpack_require__(/*! /Users/John/Work/arbres/arbres-montreal/resources/sass/app.scss */"./resources/sass/app.scss");


/***/ })

/******/ });