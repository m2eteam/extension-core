define(['jquery'], function ($) {
    'use strict';

    return function (config, element) {
        const dropdown = element.querySelector('.smart-title-items-dropdown-wrapper');
        const dropdownItems = element.querySelectorAll('.smart-title-dropdown-item');

        if (dropdown) {
            dropdown.addEventListener('click', function () {
                this.classList.toggle('open');
            });

            document.addEventListener('click', function (event) {
                if (!dropdown.contains(event.target)) {
                    dropdown.classList.remove('open');
                }
            });
        }

        dropdownItems.forEach(function (item) {
            item.addEventListener('click', function () {
                const url = this.getAttribute('data-url');
                if (url) {
                    window.location.href = url;
                }
            });
        });
    };
});
