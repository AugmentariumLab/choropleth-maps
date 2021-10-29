import {TurboColormap} from "./colormaps/turbo";

const lerp = require("lerp");
const clamp = require("clamp");
const convert = require("color-convert");

export class Colormap {
    constructor() {
        /**
         * Should be a function which maps the input value to a number between 0 and 1.
         */
        this.colorToValue = x => x / 1000.0;
        this.colormapArray = [
            [0, 0, 0],
            [255, 255, 255]
        ];
        this.nullColor = "#00000090";
    }

    /**
     * getColor - Gets a color from the given value. Not fully implemented.
     *
     * @param  {type} d Input feature value.
     * @return {type}   Color as hex string.
     */
    getColor(d) {
        if (d == null) {
            return this.nullColor;
        }
        return d > 2000
            ? "#800026"
            : d > 1000
            ? "#BD0026"
            : d > 500
            ? "#E31A1C"
            : d > 200
            ? "#FC4E2A"
            : d > 100
            ? "#FD8D3C"
            : d > 50
            ? "#FEB24C"
            : d > 10
            ? "#FED976"
            : "#FFEDA0";
    }
}
