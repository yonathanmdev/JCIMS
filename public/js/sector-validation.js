/**
 * ---------------------------------------------------------
 * Client-side Validation & Character Restriction
 * ---------------------------------------------------------
 * Usage:
 *
 * data-client-validate="true"
 *
 * data-restrict="letters"
 * data-restrict="letters-am"
 * data-restrict="letters-en"
 *
 * ---------------------------------------------------------
 */

const RESTRICT_PATTERNS = {
    letters: /[^A-Za-z\u1200-\u139F\s]/g,
    'letters-am': /[^\u1200-\u139F\s]/g,
    'letters-en': /[^A-Za-z\s]/g
};

/**
 * Remove unwanted characters while preserving cursor position.
 */
function sanitizeField(field, regex) {

    if (!field.value) return;

    const cursor = field.selectionStart ?? field.value.length;
    const original = field.value;
    const cleaned = original.replace(regex, '');

    if (original === cleaned) return;

    field.value = cleaned;

    try {
        const removed =
            original.slice(0, cursor).length -
            original.slice(0, cursor).replace(regex, '').length;

        const newCursor = Math.max(0, cursor - removed);

        field.setSelectionRange(newCursor, newCursor);
    } catch (_) {
        // Ignore unsupported input types
    }
}

document.addEventListener('DOMContentLoaded', () => {

    /**
     * =====================================================
     * Character Restriction
     * =====================================================
     */

    document.querySelectorAll('[data-restrict]').forEach(field => {

        const regex = RESTRICT_PATTERNS[field.dataset.restrict];

        if (!regex) return;

        let composing = false;

        field.addEventListener('compositionstart', () => {
            composing = true;
        });

        field.addEventListener('compositionend', () => {
            composing = false;
            sanitizeField(field, regex);
        });

        field.addEventListener('input', () => {
            if (!composing) {
                sanitizeField(field, regex);
            }
        });

        field.addEventListener('paste', () => {
            requestAnimationFrame(() => sanitizeField(field, regex));
        });

    });


    /**
     * =====================================================
     * Form Validation
     * =====================================================
     */

    document
        .querySelectorAll('form[data-client-validate="true"]')
        .forEach(form => {

            form.addEventListener('submit', function (e) {

                form.classList.add('was-validated');

                form.querySelectorAll('.is-invalid').forEach(field => {
                    field.classList.remove('is-invalid');
                    field.removeAttribute('aria-invalid');
                });

                if (form.checkValidity()) {
                    return;
                }

                e.preventDefault();
                e.stopPropagation();

                let firstInvalid = null;

                [...form.elements].forEach(field => {

                    if (
                        field.willValidate &&
                        !field.checkValidity()
                    ) {

                        field.classList.add('is-invalid');
                        field.setAttribute('aria-invalid', 'true');

                        if (!firstInvalid) {
                            firstInvalid = field;
                        }
                    }
                });

                firstInvalid?.focus();

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'ያልተሟሉ መስኮች',
                        text: 'እባክዎ ቀይ ምልክት የተደረገባቸውን መስኮች በትክክል ይሙሉ።',
                        confirmButtonText: 'እሺ'
                    });
                }

            });


            /**
             * Remove invalid state while editing.
             */

            form.querySelectorAll('input, textarea, select').forEach(field => {

                const eventName =
                    field.tagName === 'SELECT'
                        ? 'change'
                        : 'input';

                field.addEventListener(eventName, () => {

                    if (field.checkValidity()) {

                        field.classList.remove('is-invalid');
                        field.removeAttribute('aria-invalid');

                    }

                });

            });

        });

});