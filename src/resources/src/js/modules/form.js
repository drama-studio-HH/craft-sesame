import Toastify from 'toastify-js';

function initFloatingLabelFallback() {
    const inputs = document.querySelectorAll('.sesame-login-container.float-labels .field input');

    inputs.forEach(function (input) {
        updateHasValue(input);

        input.addEventListener('input', function () {
            updateHasValue(input);
        });

        input.addEventListener('animationstart', function (e) {
            if (e.animationName === 'onAutoFillStart') {
                updateHasValue(input);
            }
        });
    });
}

function updateHasValue(input) {
    if (input.value.trim() !== '') {
        input.classList.add('has-value');
    } else {
        input.classList.remove('has-value');
    }
}

function showError(input, errorEl, message) {
    input.setAttribute('aria-invalid', 'true');
    errorEl.textContent = message;
    errorEl.hidden = false;
    input.focus();
}

function clearError(input, errorEl) {
    input.setAttribute('aria-invalid', 'false');
    errorEl.textContent = '';
    errorEl.hidden = true;
}

function initFormValidation() {
    const form = document.querySelector('.sesame-login-container .form');
    if (!form) {
        return;
    }

    const emailInput = document.getElementById('email');
    if (!emailInput) {
        return;
    }

    const errorMsg = document.createElement('span');
    errorMsg.id = 'email-error';
    errorMsg.className = 'field-error';
    errorMsg.setAttribute('role', 'alert');
    errorMsg.setAttribute('aria-live', 'polite');
    errorMsg.hidden = true;

    const fieldWrapper = emailInput.closest('.field');
    if (fieldWrapper) {
        fieldWrapper.insertAdjacentElement('afterend', errorMsg);
        emailInput.setAttribute('aria-describedby', 'email-error');
    }

    form.addEventListener('submit', function (e) {
        const value = emailInput.value.trim();

        if (value === '') {
            e.preventDefault();
            showError(emailInput, errorMsg, 'Bitte geben Sie Ihre E-Mail-Adresse ein.');
            return;
        }

        if (!isValidEmail(value)) {
            e.preventDefault();
            showError(emailInput, errorMsg, 'Bitte geben Sie eine gültige E-Mail-Adresse ein.');
            return;
        }

        clearError(emailInput, errorMsg);
    });

    emailInput.addEventListener('input', function () {
        if (emailInput.getAttribute('aria-invalid') === 'true') {
            clearError(emailInput, errorMsg);
        }
    });
}

function isValidEmail(value) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
}

function initToasts() {
    const toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        return;
    }
    const toasts = toastContainer.querySelectorAll('.toast');
    toasts.forEach(toast => {
        Toastify({
            node: toast,
            duration: 10000,
            close: true,
            gravity: 'bottom',
            position: 'right'
        }).showToast();
    });
}

export {
    initFormValidation,
    initFloatingLabelFallback,
    initToasts,
};
