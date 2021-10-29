import {ShapesLoader} from "./ShapesLoader";
import {Colormap} from "./Colormap";
import {TurboColormap} from './colormaps/turbo';
import {GreenColormap} from './colormaps/colorbrewer';
import {QueryManager} from "./QueryManager";
import {MouseTooltip} from "./MouseTooltip";

const L = require("leaflet");
const leafletImage = require("leaflet-image");
APP = window.APP;

/**
 * This is the main visualizer class.
 */
export class Visualizer {
    constructor() {
        this.map = null;
        this.downloadMapButton = document.getElementById("download-map-button");
        this.queryFromSelect = document.getElementById("query-from-select");
        this.queryToNearestSelect = document.getElementById(
            "query-to-nearest-select"
        );
        this.queryDistanceCountP = document.getElementById(
            "query-distance-count-p"
        );
        this.colormap = new GreenColormap();
        this.featureToLayerMap = new Map();
        this.queryManager = new QueryManager();
        this.mouseTooltip = new MouseTooltip({
            domElement: document.getElementById("mouse-tooltip")
        });
        this.latestQueryParams = {};
    }

    /**
     * start - Starts the visualizer.
     *
     * @return {type}  description
     */
    start() {
        console.log("Visualizer started");
        this.setupMap();
        this.setupLegend();
        this.attachListeners();
    }

    /**
     * setupMap - Sets up the map.
     *
     * @return {type}  description
     */
    setupMap() {
        const self = this;
        const mapboxAccessToken = APP.mapboxAccessToken;
        const map = L.map("mapid", {
            renderer: L.canvas(),
            wheelPxPerZoomLevel: 150,
            zoomSnap: 0.1
        }).setView([37.9, -122.2], 9);
        L.Icon.Default.imagePath = "/leaflet/dist/images/";
        L.tileLayer(
            "https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=" +
                mapboxAccessToken,
            {
                id: "mapbox/streets-v11",
                attribution: "",
                tileSize: 512,
                zoomOffset: -1,
                minZoom: 1
            }
        ).addTo(map);
        map.on("moveend", this.onMapChanged.bind(this));
        const styleGenerator = feature => {
            return {
                fillColor: self.colormap.getColor(
                    feature.properties.targetProperty
                ),
                weight: 2,
                opacity: 1,
                color: "white",
                dashArray: "3",
                fillOpacity: 0.7
            };
        };
        ShapesLoader.getGeoJSON("bay_area_zip_codes").then(data => {
            self.geojson = L.geoJson(data, {
                onEachFeature: this.onEachFeature.bind(this),
                style: styleGenerator.bind(this)
            });
            self.geojson.addTo(map);
            this.onMapChanged();
        });
        this.map = map;
    }

    /**
     * setupLegend - Sets up the legend within the visualizer.
     *
     * @return {undefined}
     */
    setupLegend() {
        const self = this;
        const legend = L.control({position: "bottomright"});

        legend.onAdd = map => {
            const div = L.DomUtil.create("div", "info legend");
            // const grades = [0, 10, 50, 100, 200, 500, 1000, 2000];
            const grades = [0, 250, 500, 750, 1000, 1250, 1500, 1750, 2000];
            const labels = [];
            const unit = "meters";

            div.innerHTML +=
                '<i style="background:' +
                self.colormap.getColor(undefined) +
                '"></i> Unknown <br>';

            // loop through our density intervals and generate a label with a colored square for each interval
            for (let i = 0; i < grades.length; i++) {
                const indicatorElement = document.createElement("i");
                indicatorElement.style.background = self.colormap.getColor(
                    grades[i] + 1
                );
                div.appendChild(indicatorElement);
                if (i + 1 < grades.length) {
                    div.append(
                        document.createTextNode(
                            `${grades[i]}-${grades[i + 1]} ${unit}`
                        ),
                        document.createElement("br")
                    );
                } else {
                    div.append(
                        document.createTextNode(`${grades[i]}+ ${unit}`)
                    );
                }
            }

            return div;
        };

        legend.addTo(this.map);
        this.legend = legend;
    }

    /**
     * attachListeners - Attaches listeners to HTML elements.
     *
     * @return {undefined}
     */
    attachListeners() {
        if (this.downloadMapButton) {
            this.downloadMapButton.addEventListener("click", e => {
                e.stopPropagation();
                this.saveMap();
            });
        }
        if (this.queryFromSelect) {
            this.queryFromSelect.addEventListener(
                "change",
                this.onQueryChanged.bind(this)
            );
        }
        if (this.queryToNearestSelect) {
            this.queryToNearestSelect.addEventListener(
                "change",
                this.onQueryChanged.bind(this)
            );
        }
    }

    onQueryChanged() {
        this.clearChoroplethValues();
        this.queryAndUpdateFeatures();
    }

    clearChoroplethValues() {
        const self = this;
        const allMarkers = this.geojson.getLayers();
        const allFeatures = allMarkers.map(x => x.feature);
        allFeatures.forEach(targetFeature => {
            targetFeature.properties.targetProperty = null;
            const layer = self.featureToLayerMap.get(targetFeature);
            self.geojson.resetStyle(layer);
        });
    }

    /**
     * onEachFeature - Attaches some listeners to each feature's layer.
     *
     * @param  {type} feature Feature
     * @param  {type} layer   Layer corresponding to the feature.
     * @return {undefined}
     */
    onEachFeature(feature, layer) {
        const self = this;
        function buildTooltipText(feature) {
            // console.log("feature", feature);
            const zip = feature.properties.ZIP;
            const state = feature.properties.STATE;
            const name = feature.properties.PO_NAME;
            let targetProperty = feature.properties.targetProperty;
            if (typeof targetProperty == "undefined") {
                targetProperty = "Unknown";
            } else if (!isNaN(parseFloat(targetProperty))) {
                targetProperty = parseFloat(targetProperty).toFixed(2);
            }
            return (
                `${name}, ${state}, ${zip}\n` +
                `Value: ${targetProperty} meters`
            );
        }
        function highlightFeature(e) {
            const layer = e.target;
            const feature = e.target.feature;

            layer.setStyle({
                weight: 5,
                color: "#666",
                dashArray: "",
                fillOpacity: 0.7
            });

            if (!L.Browser.ie && !L.Browser.opera && !L.Browser.edge) {
                layer.bringToFront();
            }

            self.mouseTooltip.show({
                tooltipText: buildTooltipText(e.target.feature)
            });
        }
        function resetHighlight(e) {
            self.geojson.resetStyle(e.target);
            self.mouseTooltip.hide();
        }
        function zoomToFeature(e) {
            self.map.fitBounds(e.target.getBounds());
        }
        layer.on({
            mouseover: highlightFeature,
            mouseout: resetHighlight,
            click: zoomToFeature
        });
        this.featureToLayerMap.set(feature, layer);
    }

    onMapChanged() {
        this.queryAndUpdateFeatures();
    }

    queryAndUpdateFeatures() {
        const allMarkers = this.geojson.getLayers();
        // This is linear time. We can optimize this with a data structure later if desired.
        const visibleMarkers = allMarkers.filter(x =>
            this.map.getBounds().intersects(x._bounds)
        );
        const visibleFeatures = visibleMarkers.map(x => x.feature);
        this.updateListOfFeatures(visibleFeatures);
    }

    /**
     * queryParamsValid - Determines whether the provided query parameters are still valid.
     *
     * @param  {type} queryParams description
     * @return {type}             description
     */
    queryParamsValid(queryParams) {
        const latestParams = this.latestQueryParams;
        return (
            latestParams.query === queryParams.query &&
            latestParams.sources === queryParams.sources &&
            latestParams.targets === queryParams.targets
        );
    }

    updateListOfFeatures(featuresList) {
        const self = this;
        const zipCodeList = featuresList.map(x => x.properties.ZIP);
        const queryFrom = this.queryFromSelect.value;
        const queryTo = this.queryToNearestSelect.value;
        const queryParams = {
            query: "average_distance",
            sources: queryFrom,
            targets: queryTo,
            zipcodes: zipCodeList
        };
        this.latestQueryParams = queryParams;
        const zipToFeature = new Map();
        featuresList.forEach(x => zipToFeature.set(x.properties.ZIP, x));
        this.queryManager.queryByZip(queryParams).then(response => {
            if (!self.queryParamsValid(queryParams)) {
                return;
            }
            for (const result of response.query_results) {
                const zipCode = result.zip_code.toString();
                if (zipToFeature.has(zipCode)) {
                    const targetFeature = zipToFeature.get(zipCode);
                    targetFeature.properties.targetProperty = result.avg;
                    const layer = self.featureToLayerMap.get(targetFeature);
                    self.geojson.resetStyle(layer);
                }
            }
            if (this.queryDistanceCountP) {
                const distance_count = response.distance_count;
                this.queryDistanceCountP.innerHTML = "";
                const text = document.createTextNode(
                    "Number of Distance Computations:" + distance_count
                );
                this.queryDistanceCountP.appendChild(text);
            }
        });
    }

    /**
     * save_map - Save an image as file.
     *
     * TODO: Make it save a high resolution map.
     *
     * @return {undefined}
     */
    saveMap() {
        console.log("Saving map");
        // const mapContainer = this.map.getContainer();
        // const originalWidth = mapContainer.style.width;
        // const originalHeight = mapContainer.style.height;
        // const bounds = this.map.getBounds();
        // mapContainer.style.width = "2000px";
        // mapContainer.style.height = "2000px";
        // this.map.invalidateSize();
        // this.map.fitBounds(bounds);
        leafletImage(this.map, (err, canvas) => {
            const a = document.createElement("a");
            a.href = canvas.toDataURL();
            a.download = "output.png";
            a.click();
        });
    }
}
