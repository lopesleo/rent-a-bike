import "bootstrap/dist/css/bootstrap.min.css";
import "bootstrap";
import "../style/index.css";
import headerTpl from "./templates/header.html?raw";
import footerTpl from "./templates/footer.html?raw";
import { AutenticadorUtils } from "./Utils/AutenticadorUtils";

function renderLayout() {
    const root = document.querySelector("#app");
    if (!root) return;
    root.insertAdjacentHTML("afterbegin", headerTpl);
    root.insertAdjacentHTML("beforeend", footerTpl);
}

renderLayout();
const autenticador = new AutenticadorUtils();
