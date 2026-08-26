document.addEventListener('DOMContentLoaded', function () {
    const toggle = document.querySelector('.nav-toggle');
    const menu = document.querySelector('.nav-menu');
    const dropdowns = document.querySelectorAll('.nav-dropdown');

    if (!toggle || !menu) {
        return;
    }

    function closeMenu() {
        menu.classList.remove('is-open');
        toggle.classList.remove('is-active');
        toggle.setAttribute('aria-expanded', 'false');
        toggle.setAttribute('aria-label', 'Buka menu');
    }

    function openMenu() {
        menu.classList.add('is-open');
        toggle.classList.add('is-active');
        toggle.setAttribute('aria-expanded', 'true');
        toggle.setAttribute('aria-label', 'Tutup menu');
    }

    function closeDropdowns(except) {
        dropdowns.forEach(function (dropdown) {
            if (dropdown !== except) {
                dropdown.removeAttribute('open');
            }
        });
    }

    dropdowns.forEach(function (dropdown) {
        const summary = dropdown.querySelector('summary');

        if (!summary) {
            return;
        }

        summary.addEventListener('click', function () {
            closeDropdowns(dropdown);
        });

        dropdown.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', function () {
                closeDropdowns();
            });
        });
    });

    toggle.addEventListener('click', function () {
        const isOpen = menu.classList.contains('is-open');

        if (isOpen) {
            closeMenu();
        } else {
            openMenu();
        }
    });

    menu.querySelectorAll('a, summary').forEach(function (item) {
        item.addEventListener('click', function () {
            if (window.innerWidth <= 820) {
                closeMenu();
            }
        });
    });

    document.addEventListener('click', function (event) {
        const target = event.target;
        if (!menu.contains(target) && !toggle.contains(target)) {
            closeMenu();
            closeDropdowns();
        }
    });

    window.addEventListener('resize', function () {
        if (window.innerWidth > 820) {
            closeMenu();
        }
    });
});
