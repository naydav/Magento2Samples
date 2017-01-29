/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */

define(function () {
    'use strict';

    return {
        defaults: {
            imports: {
                toggle: ''
            },
            isVisible: false,
            inverse: false,
            values: []
        },

        /**
         * Toggle visibility state
         *
         * @param {Number} value
         */
        toggle: function (value) {
            this.isVisible = value in this.values;
            this.visible(this.inverse ? !this.isVisible : this.isVisible);
        }
    };
});
