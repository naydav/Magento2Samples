<?php
namespace Engine\JsTree\Ui\DataProvider;

use Magento\Framework\Api\SimpleBuilderInterface;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 * @method JsTreeDataInterface create()
 * @api
 */
interface JsTreeDataBuilderInterface extends SimpleBuilderInterface
{
    /**
     * @param int $treeId
     * @return $this
     */
    public function setTreeId($treeId);

    /**
     * @param bool $isNotEmpty
     * @return $this
     */
    public function setIsNotEmpty($isNotEmpty);

    /**
     * @param array $jsComponentConfig
     * @return $this
     */
    public function setJsComponentConfig(array $jsComponentConfig);
}
