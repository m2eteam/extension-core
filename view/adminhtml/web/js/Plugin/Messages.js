define([
    'jquery',
    'mage/template',
], function(jQuery, mageTemplate) {

    const SCOPE_CONTEXT = 1;
    const SCOPE_GLOBAL = 2;
    const SUCCESS = 1;
    const WARNING = 2;
    const ERROR = 3;
    const _templateContainer = '<div id="messages"><div class="messages m2e-messages"></div></div>';
    const _templates = {
        global: '<div class="message"><div><%= data %></div></div>',
        success: '<div class="message message-success success"><div data-ui-id="messages-message-success"><%= data %></div></div>',
        warning: '<div class="message message-warning warning"><div data-ui-id="messages-message-warning"><%= data %></div></div>',
        error: '<div class="message message-error error"><div data-ui-id="messages-message-error"><%= data %></div></div>',
    };

    function updateFloatingHeader () {
        const data = jQuery('.page-actions').data('floatingHeader');

        if (typeof data === 'undefined') {
            return;
        }

        data._offsetTop = data._placeholder.offset().top;
    }

    return {
        _container: '#anchor-content',

        _globalContainer: '#globalMessages',

        setContainer: function(container) {
            this._container = container;
        },

        add: function(message, scope, type) {

            let templateContainer;

            if (scope == SCOPE_GLOBAL) {
                templateContainer = jQuery(this._globalContainer).find('#messages');

                if (!templateContainer.length) {
                    jQuery(this._globalContainer).prepend(_templateContainer);
                    templateContainer = jQuery(this._globalContainer).find('#messages');
                }
            } else {
                templateContainer = jQuery(this._container).find('#messages');

                if (!templateContainer.length) {
                    var pageActions = jQuery(this._container).find('.page-main-actions');
                    if (pageActions.length) {
                        pageActions.after(_templateContainer);
                    } else {
                        jQuery(this._container).prepend(_templateContainer);
                    }
                    templateContainer = jQuery(this._container).find('#messages');
                }
            }

            let template = _templates.global;

            if (type === SUCCESS) {
                template = _templates.success;
            } else if (type == WARNING) {
                template = _templates.warning;
            } else if (type == ERROR) {
                template = _templates.error;
            }

            const messageBlock = mageTemplate(template, {
                data: message,
            });

            templateContainer.find('.messages').prepend(messageBlock);

            if (scope == SCOPE_GLOBAL) {
                updateFloatingHeader();
            }

            return this;
        },

        addSuccess: function(message) {
            return this.add(message, SCOPE_CONTEXT, SUCCESS);
        },

        addNotice: function(message) {
            return this.add(message, SCOPE_CONTEXT);
        },

        addWarning: function(message) {
            return this.add(message, SCOPE_CONTEXT, WARNING);
        },

        addError: function(message) {
            return this.add(message, SCOPE_CONTEXT, ERROR);
        },

        addGlobalSuccess: function(message) {
            return this.add(message, SCOPE_GLOBAL, SUCCESS);
        },

        addGlobalNotice: function(message) {
            return this.add(message, SCOPE_GLOBAL);
        },

        addGlobalWarning: function(message) {
            return this.add(message, SCOPE_GLOBAL, WARNING);
        },

        addGlobalError: function(message) {
            return this.add(message, SCOPE_GLOBAL, ERROR);
        },

        clear: function() {
            jQuery(this._container).find('#messages > .messages').empty();
        },

        clearGlobal: function() {
            jQuery(this._globalContainer).find('.messages').empty();
            updateFloatingHeader();
        },

        clearAll: function() {
            this.clear();
        },

        clearAllGlobal: function() {
            this.clearGlobal();
        },
    };
});
