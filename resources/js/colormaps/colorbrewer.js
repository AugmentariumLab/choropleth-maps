const lerp = require("lerp");
const clamp = require("clamp");
const convert = require("color-convert");

const green_colormap = [
  [0.969,0.988,0.992],
  [0.898,0.961,0.976],
  [0.800,0.925,0.902],
  [0.600,0.847,0.788],
  [0.400,0.761,0.643],
  [0.255,0.682,0.463],
  [0.137,0.545,0.271],
  [0.000,0.427,0.173],
  [0.000,0.267,0.106]
].reverse();

export class GreenColormap {
    constructor() {
        /**
         * Should be a function which maps the input value to a number between 0 and 1.
         */
        // this.colorToValue = x =>
        //     clamp(Math.log(x + 1) / Math.log(10000 + 1), 0, 1);
        this.colorToValue = x => clamp(x / 2000, 0, 1);
        this.colormapArray = green_colormap;
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
        const value = Math.min(
            this.colorToValue(d) * this.colormapArray.length,
            this.colormapArray.length - 1
        );
        const value_floor = Math.floor(value);
        const value_ceil = Math.ceil(value);
        const t = value - Math.floor(value);
        const floorColor = this.colormapArray[value_floor];
        const ceilColor = this.colormapArray[value_ceil];
        const rgbColor = [
            256 * lerp(floorColor[0], ceilColor[0], t),
            256 * lerp(floorColor[1], ceilColor[1], t),
            256 * lerp(floorColor[2], ceilColor[2], t)
        ];
        return `#${convert.rgb.hex(rgbColor)}`;
    }
}
