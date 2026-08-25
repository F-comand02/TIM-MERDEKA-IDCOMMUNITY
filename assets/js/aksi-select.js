(function () {
    const wrappers = document.querySelectorAll('.aksi-select-wrap');

    wrappers.forEach(function (wrapper) {
        const select = wrapper.querySelector('.aksi-level-select');

        if (!select) return;

        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'aksi-select-button';
        button.setAttribute('aria-haspopup', 'listbox');
        button.setAttribute('aria-expanded', 'false');

        const menu = document.createElement('div');
        menu.className = 'aksi-select-menu';
        menu.setAttribute('role', 'listbox');

        // Tentukan class berdasarkan value
        function getLevelClass(value) {
            switch (value) {
                case 'Mudah':
                    return 'aksi-level-mudah';

                case 'Sedang':
                    return 'aksi-level-sedang';

                case 'Sulit':
                    return 'aksi-level-sulit';

                default:
                    return 'aksi-level-all';
            }
        }

        // Update tampilan button
        function updateButton() {
            const option = select.options[select.selectedIndex];
            const levelClass = getLevelClass(option.value);

            button.classList.remove(
                'aksi-level-all',
                'aksi-level-mudah',
                'aksi-level-sedang',
                'aksi-level-sulit'
            );

            button.classList.add(levelClass);

            button.innerHTML = `
                <span>${option.textContent.trim()}</span>
                <span class="aksi-select-chevron" aria-hidden="true">⌄</span>
            `;

            // Update selected option
            menu.querySelectorAll('[role="option"]').forEach(function (item) {
                const selected = item.dataset.value === select.value;

                item.classList.toggle('is-selected', selected);
                item.setAttribute(
                    'aria-selected',
                    selected ? 'true' : 'false'
                );
            });
        }

        // Buat option custom
        Array.from(select.options).forEach(function (option) {
            const item = document.createElement('button');

            item.type = 'button';
            item.className = 'aksi-select-option';

            item.classList.add(getLevelClass(option.value));

            item.dataset.value = option.value;

            item.setAttribute('role', 'option');
            item.setAttribute('aria-selected', 'false');

            item.textContent = option.textContent.trim();

            item.addEventListener('click', function () {
                select.value = option.value;

                select.dispatchEvent(
                    new Event('change', {
                        bubbles: true
                    })
                );

                wrapper.classList.remove('is-open');

                button.setAttribute('aria-expanded', 'false');
            });

            menu.appendChild(item);
        });

        // Buka / tutup dropdown
        button.addEventListener('click', function () {
            const isOpen = wrapper.classList.toggle('is-open');

            button.setAttribute(
                'aria-expanded',
                isOpen ? 'true' : 'false'
            );
        });

        // Ketika select berubah
        select.addEventListener('change', updateButton);

        wrapper.appendChild(button);
        wrapper.appendChild(menu);

        wrapper.classList.add('has-custom-select');

        updateButton();
    });

    // Tutup dropdown kalau klik di luar
    document.addEventListener('click', function (event) {
        document
            .querySelectorAll('.aksi-select-wrap.is-open')
            .forEach(function (wrapper) {

                if (!wrapper.contains(event.target)) {
                    wrapper.classList.remove('is-open');

                    const button = wrapper.querySelector(
                        '.aksi-select-button'
                    );

                    if (button) {
                        button.setAttribute(
                            'aria-expanded',
                            'false'
                        );
                    }
                }
            });
    });
})();