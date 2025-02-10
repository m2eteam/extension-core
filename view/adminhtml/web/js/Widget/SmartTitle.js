define(['jquery'], function ($) {
    'use strict';

    return function (config, element) {
        const activeItem = element.querySelector('.smart-title-active-item');

        if (activeItem) {
            const dropdownMenu = element.querySelector('.smart-title-dropdown-menu');
            if (dropdownMenu) {
                const activeRect = activeItem.getBoundingClientRect();
                const wrapperRect = element.getBoundingClientRect();
                dropdownMenu.style.left = activeRect.left - wrapperRect.left + `px`;
                dropdownMenu.style.minWidth = activeRect.width + `px`;
            }

            activeItem.addEventListener('click', function () {
                element.classList.toggle('open');
            });

            document.addEventListener('click', function (event) {
                if (!activeItem.contains(event.target)) {
                    element.classList.remove('open');
                }
            });
        }
    };
});
