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
        const toastBg = toast.dataset.toastBg;
        const toastKey = toast.dataset.toastKey;
        const [_, bsClass] = toastKey.split('__');
        Toastify({
            className: `bg-${bsClass}`,
            style: {
                background: toastBg,
            },
            node: toast,
            duration: 10000,
            close: true,
            gravity: 'bottom',
            position: 'right'
        }).showToast();
    });
}

function initResendMail() {
    const resendMailLink = document.querySelector('.resend-mail');
    const countdownEl = document.querySelector('.resend-countdown');
    if (resendMailLink && countdownEl) {
        countdownEl.innerHTML = '60s';
        const now = Date.now();
        resendMailLink.addEventListener('click', () => {
            const diff = Date.now() - now;
            if (diff >= 60 * 1000) {
                resendMailLink.classList.remove('disabled');
                const form = resendMailLink.closest('form');
                form.submit();
            }
        });
    }
    const interval = setInterval(() => {
        const current = countdownEl.innerHTML.replace('s', '');
        if (current > 1) {
            countdownEl.innerHTML = `${current - 1}s`;
        } else {
            countdownEl.innerHTML = '';
            clearInterval(interval);
        }
    }, 1000);
}

export {
    initFormValidation,
    initFloatingLabelFallback,
    initToasts,
    initResendMail,
};
