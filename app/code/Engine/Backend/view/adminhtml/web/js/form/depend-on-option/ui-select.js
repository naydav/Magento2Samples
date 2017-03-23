/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */

define([
    'Engine_MagentoFix/js/form/element/ui-select',
    './abstract-element-processor'
], function (Element, AbstractElementProcessor) {
    'use strict';

    return Element.extend(AbstractElementProcessor);
});
