/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */

define([
    'Magento_Ui/js/form/element/single-checkbox',
    'EngineVisibilityProcessor'
], function (Element, VisibilityProcessor) {
    'use strict';

    return Element.extend(VisibilityProcessor);
});
