const path = require("path");

class DatasetManager {
    constructor() {
        this.addDatasetInputFile = document.getElementById(
            "add-dataset-input-file"
        );
        this.addDatasetFilenameSpan = document.getElementById(
            "add-dataset-filename-span"
        );
        this.addDatasetUploadButton = document.getElementById(
            "add-dataset-upload-button"
        );
        this.addDatasetNameInput = document.getElementById(
            "add-dataset-name-input"
        );
        this.addDatasetLatitudeSelect = document.getElementById(
            "add-dataset-latitude-select"
        );
        this.addDatasetLongitudeSelect = document.getElementById(
            "add-dataset-longitude-select"
        );
        this.addDatasetZipSelect = document.getElementById(
            "add-dataset-zip-select"
        );
    }

    start() {
        this.attachListeners();
    }

    onFileSelected(e) {
        this.clearMetadataColumnSelections();
        const filepath = this.addDatasetInputFile.value;
        const filename = filepath.split(/(\\|\/)/g).pop();
        const filenameWithoutExtension = filename
            .split(".")
            .slice(0, -1)
            .join(".");
        if (this.addDatasetFilenameSpan) {
            this.addDatasetFilenameSpan.innerHTML = "";
            const textNode = document.createTextNode(filename);
            this.addDatasetFilenameSpan.appendChild(textNode);
        }
        if (this.addDatasetNameInput) {
            this.addDatasetNameInput.value = filenameWithoutExtension;
        }

        const fileReader = new FileReader();
        fileReader.onload = () => {
            const firstLine = fileReader.result.split("\n")[0];
            const columnNames = firstLine.split(",");
            this.addMetadataColumnSelections(columnNames);
        };
        fileReader.readAsText(this.addDatasetInputFile.files[0]);
    }

    clearMetadataColumnSelections() {
        if (this.addDatasetLatitudeSelect) {
            this.addDatasetLatitudeSelect.innerHTML = "";
        }
        if (this.addDatasetLongitudeSelect) {
            this.addDatasetLongitudeSelect.innerHTML = "";
        }
        if (this.addDatasetZipSelect) {
            this.addDatasetZipSelect.innerHTML = "";
        }
    }

    addMetadataColumnSelections(columnNames) {
        for (const name of columnNames) {
            if (this.addDatasetLatitudeSelect) {
                const option = document.createElement("option");
                option.appendChild(document.createTextNode(name));
                this.addDatasetLatitudeSelect.appendChild(option);
            }
            if (this.addDatasetLongitudeSelect) {
                const option = document.createElement("option");
                option.appendChild(document.createTextNode(name));
                this.addDatasetLongitudeSelect.appendChild(option);
            }
            if (this.addDatasetZipSelect) {
                const option = document.createElement("option");
                option.appendChild(document.createTextNode(name));
                this.addDatasetZipSelect.appendChild(option);
            }
        }
        this.guessColumnValue(this.addDatasetLatitudeSelect, [
            "latitude",
            "lat"
        ]);
        this.guessColumnValue(this.addDatasetLongitudeSelect, [
            "longitude",
            "lon",
            "lng"
        ]);
        this.guessColumnValue(this.addDatasetZipSelect, [
            "zipcode",
            "zip",
            "postalcode",
            "zip5",
            "zip_code"
        ]);
    }

    guessColumnValue(selectElement, guesses) {
        const options = selectElement.children;
        for (let i = 0; i < options.length; i++) {
            const optionValue = options[i].value.toLowerCase();
            if (guesses.includes(optionValue)) {
                selectElement.selectedIndex = i;
                i = options.length;
            }
        }
    }

    attachListeners() {
        if (this.addDatasetInputFile) {
            this.addDatasetInputFile.addEventListener(
                "change",
                this.onFileSelected.bind(this)
            );
        }
    }
}

const app = new DatasetManager();
app.start();
