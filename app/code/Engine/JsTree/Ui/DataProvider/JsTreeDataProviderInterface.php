<?php
namespace Engine\JsTree\Ui\DataProvider;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 * @api
 */
interface JsTreeDataProviderInterface
{
    /**
     * @param int|null $entityId
     * @param bool $withRoot
     * @return JsTreeDataInterface
     */
    public function provide($entityId = null, $withRoot = false);
}
