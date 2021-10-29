export class MouseTooltip {
    constructor(options = {}) {
        this.domElement = options.domElement;
        if (this.domElement == null) {
            console.error("Invalid dom element");
        }
        this.mouseMoveCallback = this.onMouseMove.bind(this);
    }

    attachListener() {
        document.addEventListener("mousemove", this.mouseMoveCallback);
    }

    removeListener() {
        document.removeEventListener("mousemove", this.mouseMoveCallback);
    }

    onMouseMove(e) {
        this.domElement.style.top = e.clientY + "px";
        this.domElement.style.left = e.clientX + "px";
    }

    hide() {
        this.removeListener();
        this.domElement.style.top = "";
        this.domElement.style.left = "";
    }

    show(options) {
        this.attachListener();
        if (options.tooltipText) {
            this.domElement.dataset.tooltip = options.tooltipText;
        }
    }
}
