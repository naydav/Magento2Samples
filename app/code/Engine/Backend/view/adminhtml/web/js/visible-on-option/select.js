/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */

define([
    'Magento_Ui/js/form/element/select',
    'EngineVisibilityProcessor'
], function (Element, VisibilityProcessor) {
    'use strict';

    return Element.extend(VisibilityProcessor);
});
