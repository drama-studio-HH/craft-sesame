import {initFloatingLabelFallback, initFormValidation, initResendMail, initToasts} from "./modules/form.js";
import "../scss/_form.scss";

document.addEventListener('DOMContentLoaded', () => {
    initFloatingLabelFallback();
    initFormValidation();
    initToasts();
    initResendMail();
});