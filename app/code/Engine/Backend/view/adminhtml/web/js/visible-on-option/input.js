/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */

define([
    'Magento_Ui/js/form/element/abstract',
    'EngineVisibilityProcessor'
], function (Element, VisibilityProcessor) {
    'use strict';

    return Element.extend(VisibilityProcessor);
});
