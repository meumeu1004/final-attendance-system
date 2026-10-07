// UI-only helpers: live password checks, photo preview, today's date, demo navigation.

const $ = (selector) => document.querySelector(selector);

const password = $('#password');
const confirmPassword = $('#confirm');

// ---------- Password checklist, strength meter, match message ----------
if (password) {
    const rules = {
        len: (v) => v.length > 6,
        up: (v) => /[A-Z]/.test(v),
        low: (v) => /[a-z]/.test(v),
        sp: (v) => /[^A-Za-z0-9]/.test(v)
    };

    const meter = $('.meter');
    const meterLabel = meter.querySelector('b');

    function checkPassword() {
        let passed = 0;

        for (const key in rules) {
            const ok = rules[key](password.value);
            $('[data-rule=' + key + ']').classList.toggle('ok', ok);
            if (ok) passed++;
        }

        let level = '';
        if (password.value) {
            if (passed <= 2) level = 'weak';
            else if (passed === 3) level = 'medium';
            else level = 'strong';
        }

        meter.dataset.level = level;
        meterLabel.textContent = level ? 'Password strength: ' + level : '';

        checkMatch();
    }

    function checkMatch() {
        const message = $('.match');

        if (!confirmPassword.value) {
            message.textContent = '';
            message.className = 'match';
            confirmPassword.setCustomValidity('');
            return;
        }

        const same = confirmPassword.value === password.value;
        confirmPassword.setCustomValidity(same ? '' : 'Passwords do not match');
        message.textContent = same ? '✓ Passwords match' : 'Passwords do not match';
        message.className = 'match ' + (same ? 'good' : 'bad');
    }

    password.addEventListener('input', checkPassword);
    confirmPassword.addEventListener('input', checkMatch);
}

// ---------- Profile picture preview ----------
const photo = $('#photo');

if (photo) {
    photo.addEventListener('change', () => {
        const file = photo.files[0];
        if (file) $('#preview').src = URL.createObjectURL(file);
    });
}

// ---------- Today's date (mm/dd/yyyy) ----------
document.querySelectorAll('[data-today]').forEach((el) => {
    el.textContent = new Date().toLocaleDateString('en-US', {
        month: '2-digit',
        day: '2-digit',
        year: 'numeric'
    });
});

// ---------- Demo only: the backend developer replaces this with real submit handling ----------
document.querySelectorAll('form[data-next]').forEach((form) => {
    form.addEventListener('submit', (event) => {
        event.preventDefault();
        const next = form.dataset.next;

        if (next.startsWith('#')) location.hash = next;
        else location.href = next;
    });
});

// ---------- Show / hide password (works for every password field) ----------
document.addEventListener('click', (event) => {
    const button = event.target.closest('.pw-toggle');
    if (!button) return;

    const input = button.parentElement.querySelector('input');
    const show = input.type === 'password';

    input.type = show ? 'text' : 'password';
    button.setAttribute('aria-pressed', show);
    button.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
});