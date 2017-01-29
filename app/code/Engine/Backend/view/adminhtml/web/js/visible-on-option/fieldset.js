/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */

define([
    'Magento_Ui/js/form/components/fieldset',
    'EngineVisibilityProcessor'
], function (Fieldset, VisibilityProcessor) {
    'use strict';

    return Fieldset.extend(VisibilityProcessor).extend(
        {
            defaults: {
                openOnShow: true
            },

            /**
             * Toggle visibility state
             */
            toggle: function () {
                this._super();

                if (this.openOnShow) {
                    this.opened(this.inverse ? !this.isVisible : this.isVisible);
                }
            }
        }
    );
});
