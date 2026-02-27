(function () {
    const uploadInput = document.getElementById('upload-img');
    if (uploadInput) {
        uploadInput.addEventListener('change', function (event) {
            const img = document.getElementById('preview-img');
            const plus = document.querySelector('.plus-icon');
            const selectedFile = event.target.files && event.target.files[0];

            if (!img || !plus || !selectedFile) {
                return;
            }

            img.src = URL.createObjectURL(selectedFile);
            img.style.display = 'block';
            plus.style.opacity = '0';
        });
    }

    const roleSelect = document.getElementById('ruolo-select');
    if (roleSelect) {
        const updateRoleFields = function () {
            const piva = document.getElementById('piva-container');
            const azienda = document.getElementById('azienda-container');

            if (!piva || !azienda) {
                return;
            }

            if (roleSelect.value === 'cliente') {
                piva.style.display = 'block';
                azienda.style.display = 'block';
            } else {
                piva.style.display = 'none';
                azienda.style.display = 'none';
            }
        };

        roleSelect.addEventListener('change', updateRoleFields);
        updateRoleFields();
    }

    const countryPrefixes = window.ticketingCountryPrefixMap || {};
    const countrySelect = document.getElementById('nazione-select');
    const phonePrefixInput = document.getElementById('phone-prefix');
    const phoneInput = document.getElementById('phone-number');

    if (!countrySelect || !phonePrefixInput || !phoneInput) {
        return;
    }

    const onlyDigits = function (value) {
        return (value || '').replace(/\D+/g, '');
    };

    const stripKnownPrefix = function (digits, prefix) {
        const prefixDigits = onlyDigits(prefix);
        if (prefixDigits !== '' && digits.startsWith(prefixDigits)) {
            return digits.slice(prefixDigits.length);
        }

        return digits;
    };

    const applySelectedPrefix = function () {
        const selectedPrefix = countryPrefixes[countrySelect.value] || '';
        const previousPrefix = phonePrefixInput.dataset.currentPrefix || '';
        phonePrefixInput.value = selectedPrefix;
        phonePrefixInput.dataset.currentPrefix = selectedPrefix;
        phoneInput.placeholder = selectedPrefix !== '' ? selectedPrefix + ' 3331234567' : '3331234567';

        if (selectedPrefix === '') {
            return;
        }

        const rawValue = phoneInput.value.trim();
        if (rawValue === '') {
            return;
        }

        let digits = onlyDigits(rawValue);
        digits = stripKnownPrefix(digits, previousPrefix);
        digits = stripKnownPrefix(digits, selectedPrefix);

        if (digits === '') {
            phoneInput.value = '';
            return;
        }

        phoneInput.value = selectedPrefix + digits;
    };

    countrySelect.addEventListener('change', applySelectedPrefix);
    phoneInput.addEventListener('blur', applySelectedPrefix);

    const registerForm = countrySelect.closest('form');
    if (registerForm) {
        registerForm.addEventListener('submit', applySelectedPrefix);
    }

    applySelectedPrefix();
})();
