document.addEventListener('DOMContentLoaded', function () {
    // Password strength indicator
    const passwordInput = document.getElementById('password');
    if (passwordInput) {
        passwordInput.addEventListener('input', function () {
            const strengthBar = document.querySelector('.password-strength-bar');
            if (strengthBar) {
                const strength = calculatePasswordStrength(this.value);
                strengthBar.style.width = strength.percentage + '%';
                strengthBar.style.backgroundColor = strength.color;
            }
        });
    }

    // Form validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function (e) {
            let isValid = true;
            const inputs = this.querySelectorAll('input[required]');

            inputs.forEach(input => {
                if (!input.value.trim()) {
                    isValid = false;
                    input.classList.add('error');
                    const errorMsg = document.createElement('div');
                    errorMsg.className = 'error-message';
                    errorMsg.textContent = 'This field is required';
                    errorMsg.style.color = 'var(--danger-color)';
                    errorMsg.style.fontSize = '0.8rem';
                    errorMsg.style.marginTop = '0.3rem';
                    input.parentNode.appendChild(errorMsg);
                }
            });

            if (!isValid) {
                e.preventDefault();
            }
        });
    });

    // Clear error on input
    const requiredInputs = document.querySelectorAll('input[required]');
    requiredInputs.forEach(input => {
        input.addEventListener('input', function () {
            if (this.value.trim()) {
                this.classList.remove('error');
                const errorMsg = this.parentNode.querySelector('.error-message');
                if (errorMsg) {
                    errorMsg.remove();
                }
            }
        });
    });

    // Login/Register form toggle
    const loginForm = document.querySelector('.login-form');
    const registerForm = document.querySelector('.register-form');
    const toggleForms = document.querySelectorAll('.toggle-form');

    if (toggleForms.length && loginForm && registerForm) {
        toggleForms.forEach(link => {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                loginForm.classList.toggle('hidden');
                registerForm.classList.toggle('hidden');
            });
        });
    }
});

function calculatePasswordStrength(password) {
    let strength = 0;

    // Length check
    if (password.length > 0) strength += 10;
    if (password.length >= 8) strength += 20;
    if (password.length >= 12) strength += 20;

    // Character diversity
    if (/[A-Z]/.test(password)) strength += 15;
    if (/[0-9]/.test(password)) strength += 15;
    if (/[^A-Za-z0-9]/.test(password)) strength += 20;

    // Cap at 100
    strength = Math.min(strength, 100);

    // Determine color
    let color;
    if (strength < 30) color = 'var(--danger-color)';
    else if (strength < 70) color = 'var(--warning-color)';
    else color = 'var(--success-color)';

    return {
        percentage: strength,
        color: color
    };
}

// AJAX form submission example (optional)
function submitFormAjax(form, callback) {
    const formData = new FormData(form);
    const xhr = new XMLHttpRequest();

    xhr.open(form.method, form.action, true);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

    xhr.onload = function () {
        if (xhr.status >= 200 && xhr.status < 300) {
            callback(null, JSON.parse(xhr.responseText));
        } else {
            callback(new Error('Request failed'), null);
        }
    };

    xhr.onerror = function () {
        callback(new Error('Request failed'), null);
    };

    xhr.send(formData);
}