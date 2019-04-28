<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Arbres de Montréal</title>
        
        <link href="{{asset('css/app.css') }}" rel="stylesheet" />

    </head>
    <body>

        <div class="map"></div>
        <div class="loading"><p>Loading...</p></div>
        

        <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps.key') }}&callback=initMap"
    async defer></script>

        <script src="{{asset('js/app-google.js')}}" async></script>
    </body>
</html>
