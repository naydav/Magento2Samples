/**
 * Add possibility to unset value from select
 *
 * @author  naydav <valeriy.nayda@gmail.com>
 */
define([
    'underscore',
    'Magento_Ui/js/form/element/ui-select'
], function (_, UiSelect) {
    'use strict';

    return UiSelect.extend({
        /**
         * Toggle activity list element
         *
         * @param {Object} data - selected option data
         * @returns {Object} Chainable
         */
        toggleOptionSelected: function (data) {
            var isSelected = this.isSelected(data.value);

            if (this.lastSelectable && data.hasOwnProperty(this.separator)) {
                return this;
            }

            if (!this.multiple) {
                if (!isSelected) {
                    this.value(data.value);
                } else {
                    // Add possibility to unset value from select
                    this.value('');
                }
                this.listVisible(false);
            } else {
                if (!isSelected) { /*eslint no-lonely-if: 0*/
                    this.value.push(data.value);
                } else {
                    this.value(_.without(this.value(), data.value));
                }
            }

            return this;
        }
    });
});
