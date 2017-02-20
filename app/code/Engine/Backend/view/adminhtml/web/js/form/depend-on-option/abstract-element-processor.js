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
         * Process visibility state
         *
         * @param {String} value
         */
        processVisibility: function (value) {
            this.isVisible = value in this.values;
            if (this.isVisible) {
                this.inverse ? this._disable() : this._enable();
            } else {
                !this.inverse ? this._disable() : this._enable();
            }
        },

        _enable: function () {
            this.visible(true);
            this.enable();
        },

        _disable: function () {
            this.visible(false);
            this.disable();
            this.clear();
            // prevent sent to server disabled inputs
            this.source.remove(this.dataScope);
        }
    };
});
