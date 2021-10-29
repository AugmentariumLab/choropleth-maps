const geoJsonDir = "/geojson";
const geoJsonNameToPath = {
    bay_area_zip_codes: geoJsonDir + "/bay_area_zip_codes.json",
    us_zip_codes: geoJsonDir + "/us-zip-codes.geojson",
    us_states: geoJsonDir + "/us-states.json"
};
const geojsonCache = new Map();
export class ShapesLoader {
    static async getGeoJSON(name) {
      if (!geojsonCache.has(name)) {
        const result = await fetch(geoJsonNameToPath[name]).then(response => response.json());
        geojsonCache.set(name, result);
        return result;
      } else {
        return geojsonCache.get(name);
      }
    }
}
