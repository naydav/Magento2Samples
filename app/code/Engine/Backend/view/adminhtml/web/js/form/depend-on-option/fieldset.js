/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */

define([
    'Magento_Ui/js/form/components/fieldset'
], function (Fieldset) {
    'use strict';

    return Fieldset.extend({
        defaults: {
            imports: {
                processVisibility: ''
            },
            isVisible: false,
            inverse: false,
            values: [],
            openOnShow: true
        },

        /**
         * Process visibility state
         *
         * @param {String} value
         */
        processVisibility: function (value) {
            this.isVisible = value in this.values;

            if (this.isVisible) {
                this.visible(!this.inverse);
            } else {
                this.visible(this.inverse);
            }

            if (this.openOnShow) {
                this.opened(this.inverse ? !this.isVisible : this.isVisible);
            }
        }
    });
});
