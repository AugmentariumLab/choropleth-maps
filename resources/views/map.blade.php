<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <title>{{ Config::get('app.name') }}</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"
    integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A=="
    crossorigin=""/>
    <link rel="stylesheet" href="/css/app.css" />
    <link rel="stylesheet" href="/css/map.css" />
    <script>
        APP = window.APP || {};
        APP.mapboxAccessToken = "{{ Config::get('app.mapbox_access_token') }}";
        APP.APP_URL = "{{ Config::get('app.url') }}";
        window.APP = APP;
    </script>
    <script src="js/app.js" async defer></script>
</head>

<body>
    @include('navbar')
    <div id="map-parent-container">
        <div id="mapid">
        </div>
        {{-- <div id="download-controls-div">
            <button id="download-map-button" class="button">Save Map</button>
        </div> --}}

        <div id="query-constructor-modal" class="modal__container" aria-hidden="false">
            <header>
                <h2 class="modal__title">
                    Query Manager
                </h2>
            </header>
            <div class="modal__content">
                <div class="field is-horizontal">
                    <div class="field-label">
                        <label class="label">Query</label>
                    </div>
                    <div class="field-body">
                        <div class="select is-fullwidth">
                            <select>
                                <option>Average Distance</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="field is-horizontal">
                    <div class="field-label">
                        <label class="label">From</label>
                    </div>
                    <div class="field-body">
                        <div class="select is-fullwidth">
                            <select name="query-from" id="query-from-select">
                                @foreach ($existing_datasets as $dataset)
                                <option value="{{$dataset->dataset_name}}">{{$dataset->dataset_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="field is-horizontal">
                    <div class="field-label">
                        <label class="label">To Nearest</label>
                    </div>
                    <div class="field-body">
                        <div class="select is-fullwidth">
                            <select name="query-to-nearest" id="query-to-nearest-select">
                                @foreach ($existing_datasets as $dataset)
                                <option value="{{$dataset->dataset_name}}">{{$dataset->dataset_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="field is-horizontal">
                    <div class="field-label">
                        <label class="label">By</label>
                    </div>
                    <div class="field-body">
                        <div class="select is-fullwidth">
                            <select>
                                <option>Zip Code</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="field is-horizontal">
                  <p id="query-distance-count-p"></p>
                </div>
            </div>

        </div>
    </div>
    <span class="has-tooltip-arrow has-tooltip-active" data-tooltip="Tooltip content" id="mouse-tooltip" tabindex="-1"></span>
</body>

</html>
