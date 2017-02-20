/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */

define([
    'Magento_Ui/js/form/element/select',
    './abstract-element-processor'
], function (Element, AbstractElementProcessor) {
    'use strict';

    return Element.extend(AbstractElementProcessor);
});
