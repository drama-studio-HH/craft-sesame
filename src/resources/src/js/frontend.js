import {initFloatingLabelFallback, initFormValidation, initResendMail, initToasts} from "./modules/form.js";
import '../scss/frontend.scss';

document.addEventListener('DOMContentLoaded', () => {
    initFloatingLabelFallback();
    initFormValidation();
    initToasts();
    initResendMail();
});
