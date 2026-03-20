import {initFloatingLabelFallback, initFormValidation, initToasts} from "./modules/form.js";
import "../scss/_form.scss";

document.addEventListener('DOMContentLoaded', () => {
    initFloatingLabelFallback();
    initFormValidation();
    initToasts();
});