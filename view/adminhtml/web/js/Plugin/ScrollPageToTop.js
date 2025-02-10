define([], function () {
    'use strict';

    return {
        scrollPageToTop: function () {
            if (location.href[location.href.length - 1] != '#') {
                setLocation(location.href + '#');
            } else {
                setLocation(location.href);
            }
        }
    };
});
