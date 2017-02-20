/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */

define([
    'jquery',
    'Magento_Ui/js/lib/validation/validator',
    'uiRegistry'
], function ($, validator, uiRegistry) {
    'use strict';

    return function (target) {
        validator.addRule(
            'validate-greater-than-field-value',
            function (value, params) {
                var valueToCompare = uiRegistry.get(params.field).value();
                if ($.isNumeric(value) && $.isNumeric(valueToCompare)) {
                    return parseFloat(value) > parseFloat(valueToCompare);
                }
                return true;
            }
        );
        return target;
    };
});
