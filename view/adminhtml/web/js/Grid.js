define([
    'M2ECore/Plugin/Confirm',
    'M2ECore/Plugin/ScrollPageToTop',
    'M2ECore/Plugin/Alert',
    'mage/translate'
], function (confirm, scroll, alert, $t) {
    window.M2ECoreGrid = Class.create({

        initialize: function (gridId) {
            this.gridId = gridId;
            this.prepareActions();
        },

        afterInitPage: function () {
            const submitButton = $$('#' + this.gridId + '_massaction-form button');

            submitButton.each((function (s) {
                s.writeAttribute("onclick", '');
                s.observe('click', (function () {
                    this.massActionSubmitClick();
                }).bind(this));
            }).bind(this));
        },

        getGridObj: function () {
            return window[this.gridId + 'JsObject'];
        },

        getGridMassActionObj: function () {
            return window[this.gridId + '_massactionJsObject'];
        },

        getCellContent: function (rowId, cellIndex) {
            const rows = this.getGridObj().rows;

            for (let i = 0; i < rows.length; i++) {
                const row = rows[i];
                const cells = $(row).childElements();

                let checkbox = $(cells[0]).select('input');
                checkbox = checkbox[0];

                if (checkbox && checkbox.value == rowId) {
                    return trim(cells[cellIndex].innerHTML);
                }
            }

            return '';
        },

        getProductNameByRowId: function (rowId) {
            const cellContent = this.getCellContent(rowId, this.productTitleCellIndex);
            const expr = new RegExp(/<span[^>]*>(.*?)<\/span>/i);
            const matches = expr.exec(cellContent);

            return (matches && !Object.isUndefined(matches[1])) ? matches[1] : '';
        },

        selectAll: function () {
            this.getGridMassActionObj().selectAll();
        },

        unselectAll: function () {
            this.getGridMassActionObj().unselectAll();
        },

        unselectAllAndReload: function () {
            this.unselectAll();
            this.getGridObj().reload();
        },

        selectByRowId: function (rowId) {
            this.unselectAll();

            const rows = this.getGridObj().rows;
            for (let i = 0; i < rows.length; i++) {
                const row = rows[i];
                const cells = $(row).childElements();

                let checkbox = $(cells[0]).select('input');
                checkbox = checkbox[0];

                if (checkbox.value == rowId) {
                    checkbox.checked = true;
                    this.getGridMassActionObj().checkedString = rowId.toString();
                    break;
                }
            }
        },

        getSelectedProductsString: function () {
            return this.getGridMassActionObj().checkedString
        },

        getSelectedProductsArray: function () {
            return this.getSelectedProductsString().split(',');
        },

        getOrderedSelectedProductsArray: function () {
            const selectedProductsArray = this.getSelectedProductsArray();
            const checkboxesValuesArray = this.getGridMassActionObj().getCheckboxesValuesAsString().split(',');

            const orderedSelectedProductArray = [];

            checkboxesValuesArray.forEach(function (value) {
                if (selectedProductsArray.indexOf(value) >= 0) {
                    orderedSelectedProductArray.push(value);
                }
            });

            return orderedSelectedProductArray;
        },

        massActionSubmitClick: function () {
            if (this.validateItemsForMassAction() === false) {
                return;
            }

            const self = this;
            let selectAction = true;
            $$('select#' + self.gridId + '_massaction-select option').each(function (o) {
                if (o.selected && o.value == '') {
                    self.alert($t('Please select Action.'));
                    selectAction = false;

                    return;
                }
            });

            if (!selectAction) {
                return;
            }

            scroll.scrollPageToTop();

            confirm({
                actions: {
                    confirm: function () {
                        $$('select#' + self.gridId + '_massaction-select option').each(function (o) {

                            if (!o.selected) {
                                return;
                            }

                            if (!o.value || !self.actions[o.value + 'Action']) {
                                self.alert($t('Please select Action.'));
                                return;
                            }

                            self.actions[o.value + 'Action']();

                        });
                    },
                    cancel: function () {
                        return false;
                    }
                }
            });
        },

        validateItemsForMassAction: function () {
            if (this.getSelectedProductsString() === '' || this.getSelectedProductsArray().length === 0) {
                this.alert($t('Please select Items.'));

                return false;
            }

            return true;
        },

        prepareActions: function () {
            alert('abstract prepareActions');
        },

        alert: function (text, callback) {
            alert(text, {
                actions: {
                    cancel: callback || function () {
                    }
                },
                content: text
            });
        },
    });
});
