
(function () {
    function getMessage(field) {
        var label = field.dataset.label || 'Kolom ini';
        var value = field.value;
        var empty = field.type === 'password' ? value === '' : value.trim() === '';

        if (field.required && empty) return label + ' wajib diisi.';
        if (empty) return '';

        if (field.type === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value.trim())) {
            return 'Format email tidak valid. Contoh: nama@kampus.ac.id';
        }
        if (field.minLength > 0 && value.length < field.minLength) {
            return label + ' minimal ' + field.minLength + ' karakter.';
        }
        if (field.maxLength > 0 && value.length > field.maxLength) {
            return label + ' maksimal ' + field.maxLength + ' karakter.';
        }
        if (field.dataset.match) {
            var other = field.form.elements[field.dataset.match];
            if (other && value !== other.value) return label + ' tidak cocok.';
        }
        return '';
    }

    function show(field, text) {
        var box = field.form.querySelector('[data-error="' + field.name + '"]');
        if (box) {
            box.textContent = text;
            box.classList.toggle('hidden', text === '');
        }
        field.classList.toggle('border-red-400', text !== '');
        field.setAttribute('aria-invalid', text !== '' ? 'true' : 'false');
        return text === '';
    }

    function check(field) {
        return show(field, getMessage(field));
    }

    document.querySelectorAll('form[data-validate]').forEach(function (form) {
        var fields = Array.prototype.filter.call(form.elements, function (el) {
            return el.name && el.dataset.label;
        });

        fields.forEach(function (field) {
            field.addEventListener('blur', function () { check(field); });

            field.addEventListener('input', function () {
                // Kalau lagi menampilkan error (dari client maupun server), cek ulang saat mengetik
                var box = form.querySelector('[data-error="' + field.name + '"]');
                if (box && !box.classList.contains('hidden')) check(field);

                // Sandi berubah -> cek ulang kolom konfirmasi yang bergantung padanya
                fields.forEach(function (other) {
                    if (other.dataset.match === field.name && other.value !== '') check(other);
                });
            });
        });

        form.addEventListener('submit', function (e) {
            var firstInvalid = null;
            fields.forEach(function (field) {
                if (!check(field) && !firstInvalid) firstInvalid = field;
            });
            if (firstInvalid) {
                e.preventDefault();
                firstInvalid.focus();
            }
        });
    });
})();