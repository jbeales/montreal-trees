<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Arbres de Montréal</title>

        <link href="{{asset('css/app.css') }}" rel="stylesheet" />

        <script src='https://api.mapbox.com/mapbox-gl-js/v1.12.0/mapbox-gl.js'></script>
        <link href='https://api.mapbox.com/mapbox-gl-js/v1.12.0/mapbox-gl.css' rel='stylesheet' />
        <script>
            mapboxgl.accessToken = '{{ config('services.mapbox.key') }}';
        </script>

    </head>
    <body>

        <div class="map"></div>
        <div class="loading"><p>Loading...</p></div>
        <div class="filter-control-holder"><input type="search" name="filter-text" id="filter-text" placeholder="search / cherchez"></div>


<!--         <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps.key') }}&callback=initMap"
    async defer></script>

-->

        <script src="{{asset('js/app.js')}}" async></script>
    </body>
</html>
