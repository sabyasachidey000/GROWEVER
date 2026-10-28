<style>
    .kiosk-keyboard-open {
        padding-bottom: 220px;
    }

    .kiosk-keyboard {
        background: #111827;
        border-top: 1px solid rgba(255, 255, 255, 0.12);
        bottom: 0;
        box-shadow: 0 -8px 24px rgba(15, 23, 42, 0.22);
        color: #fff;
        display: none;
        left: 0;
        padding: 9px;
        position: fixed;
        right: 0;
        z-index: 2050;
    }

    .kiosk-keyboard.is-visible {
        display: block;
    }

    .kiosk-keyboard__preview {
        background: #1f2937;
        border: 1px solid #374151;
        border-radius: 6px;
        color: #e5e7eb;
        font-size: 0.95rem;
        min-height: 36px;
        overflow: hidden;
        padding: 6px 9px;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .kiosk-keyboard__row {
        display: flex;
        gap: 6px;
        justify-content: center;
        margin-top: 6px;
    }

    .kiosk-keyboard__key {
        align-items: center;
        background: #f9fafb;
        border: 0;
        border-radius: 6px;
        color: #111827;
        display: inline-flex;
        font-size: 0.95rem;
        font-weight: 600;
        justify-content: center;
        min-height: 40px;
        min-width: 40px;
        padding: 0 11px;
        touch-action: manipulation;
        user-select: none;
    }

    .kiosk-keyboard__key:active,
    .kiosk-keyboard__key.is-active {
        background: #93c5fd;
    }

    .kiosk-keyboard__key--wide {
        min-width: 80px;
    }

    .kiosk-keyboard__key--space {
        min-width: min(340px, 40vw);
    }

    .kiosk-keyboard__key--danger {
        background: #fee2e2;
        color: #991b1b;
    }

    .kiosk-keyboard__key--primary {
        background: #2563eb;
        color: #fff;
    }

    @media (max-width: 768px) {
        .kiosk-keyboard-open {
            padding-bottom: 285px;
        }

        .kiosk-keyboard {
            padding: 6px;
        }

        .kiosk-keyboard__row {
            gap: 4px;
        }

        .kiosk-keyboard__key {
            font-size: 0.86rem;
            min-height: 38px;
            min-width: 28px;
            padding: 0 7px;
        }

        .kiosk-keyboard__key--wide {
            min-width: 58px;
        }

        .kiosk-keyboard__key--space {
            min-width: 36vw;
        }
    }
</style>

<div class="kiosk-keyboard" id="kioskKeyboard" aria-label="On-screen keyboard">
    <div class="container-fluid px-0">
        <div class="kiosk-keyboard__preview mb-2" id="kioskKeyboardPreview"></div>
        <div id="kioskKeyboardRows"></div>
    </div>
</div>

<script>
    (function() {
        const keyboard = document.getElementById('kioskKeyboard');
        const rows = document.getElementById('kioskKeyboardRows');
        const preview = document.getElementById('kioskKeyboardPreview');

        if (!keyboard || !rows || !preview) {
            return;
        }

        const textLayout = [
            ['1', '2', '3', '4', '5', '6', '7', '8', '9', '0'],
            ['q', 'w', 'e', 'r', 't', 'y', 'u', 'i', 'o', 'p'],
            ['a', 's', 'd', 'f', 'g', 'h', 'j', 'k', 'l'],
            ['shift', 'z', 'x', 'c', 'v', 'b', 'n', 'm', 'backspace'],
            ['clear', 'space', '.', '-', 'hide'],
        ];
        const numberLayout = [
            ['1', '2', '3'],
            ['4', '5', '6'],
            ['7', '8', '9'],
            ['clear', '0', '.'],
            ['backspace', 'hide'],
        ];

        let activeField = null;
        let shift = false;

        function enabledFields() {
            return document.querySelectorAll('[data-kiosk-keyboard]');
        }

        function isNumberField(field) {
            return field.inputMode === 'decimal' || field.inputMode === 'numeric' || field.type === 'number';
        }

        function show(field) {
            activeField = field;
            shift = false;
            render();
            updatePreview();
            keyboard.classList.add('is-visible');
            document.body.classList.add('kiosk-keyboard-open');
        }

        function hide() {
            keyboard.classList.remove('is-visible');
            document.body.classList.remove('kiosk-keyboard-open');
            activeField = null;
        }

        function updatePreview() {
            preview.textContent = activeField ? activeField.value : '';
        }

        function cursorStart() {
            try {
                return activeField.selectionStart ?? activeField.value.length;
            } catch (error) {
                return activeField.value.length;
            }
        }

        function cursorEnd() {
            try {
                return activeField.selectionEnd ?? activeField.value.length;
            } catch (error) {
                return activeField.value.length;
            }
        }

        function setCursor(position) {
            try {
                activeField.setSelectionRange?.(position, position);
            } catch (error) {
                return;
            }
        }

        function dispatchFieldEvents() {
            activeField.dispatchEvent(new Event('input', { bubbles: true }));
            activeField.dispatchEvent(new Event('change', { bubbles: true }));
            updatePreview();
        }

        function insertText(text) {
            const start = cursorStart();
            const end = cursorEnd();
            activeField.value = activeField.value.slice(0, start) + text + activeField.value.slice(end);
            const nextPosition = start + text.length;
            setCursor(nextPosition);
            dispatchFieldEvents();
        }

        function backspace() {
            const start = cursorStart();
            const end = cursorEnd();

            if (start !== end) {
                activeField.value = activeField.value.slice(0, start) + activeField.value.slice(end);
                setCursor(start);
            } else if (start > 0) {
                activeField.value = activeField.value.slice(0, start - 1) + activeField.value.slice(start);
                setCursor(start - 1);
            }

            dispatchFieldEvents();
        }

        function clearField() {
            activeField.value = '';
            activeField.focus();
            dispatchFieldEvents();
        }

        function keyLabel(key) {
            const labels = {
                backspace: 'Back',
                clear: 'Clear',
                hide: 'Done',
                shift: 'Shift',
                space: 'Space',
            };

            return labels[key] || (shift ? key.toUpperCase() : key);
        }

        function keyClass(key) {
            const classes = ['kiosk-keyboard__key'];

            if (['backspace', 'clear', 'hide', 'shift'].includes(key)) {
                classes.push('kiosk-keyboard__key--wide');
            }

            if (key === 'space') {
                classes.push('kiosk-keyboard__key--space');
            }

            if (key === 'clear') {
                classes.push('kiosk-keyboard__key--danger');
            }

            if (key === 'hide') {
                classes.push('kiosk-keyboard__key--primary');
            }

            if (key === 'shift' && shift) {
                classes.push('is-active');
            }

            return classes.join(' ');
        }

        function handleKey(key) {
            if (!activeField) {
                return;
            }

            activeField.focus();

            if (key === 'hide') {
                hide();
                return;
            }

            if (key === 'backspace') {
                backspace();
                return;
            }

            if (key === 'clear') {
                clearField();
                return;
            }

            if (key === 'shift') {
                shift = !shift;
                render();
                return;
            }

            insertText(key === 'space' ? ' ' : (shift ? key.toUpperCase() : key));

            if (shift && /^[a-z]$/.test(key)) {
                shift = false;
                render();
            }
        }

        function render() {
            const layout = activeField && isNumberField(activeField) ? numberLayout : textLayout;
            rows.innerHTML = '';

            layout.forEach(function(row) {
                const rowElement = document.createElement('div');
                rowElement.className = 'kiosk-keyboard__row';

                row.forEach(function(key) {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = keyClass(key);
                    button.textContent = keyLabel(key);
                    button.addEventListener('pointerdown', function(event) {
                        event.preventDefault();
                        handleKey(key);
                    });
                    rowElement.appendChild(button);
                });

                rows.appendChild(rowElement);
            });
        }

        document.addEventListener('focusin', function(event) {
            if (event.target.matches('[data-kiosk-keyboard]')) {
                show(event.target);
            }
        });

        document.addEventListener('input', function(event) {
            if (event.target === activeField) {
                updatePreview();
            }
        });

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && activeField) {
                hide();
            }
        });

        enabledFields().forEach(function(field) {
            field.setAttribute('autocomplete', 'off');
        });
    })();
</script>
