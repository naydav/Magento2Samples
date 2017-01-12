<?php
namespace Engine\JsTree\Ui\DataProvider;

use Magento\Framework\Api\AbstractSimpleObjectBuilder;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 * @method JsTreeDataInterface create()
 */
class JsTreeDataBuilder extends AbstractSimpleObjectBuilder implements JsTreeDataBuilderInterface
{
    /**
     * {@inheritdoc}
     */
    public function setTreeId($treeId)
    {
        $this->data[JsTreeDataInterface::TREE_ID] = $treeId;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setIsNotEmpty($isNotEmpty)
    {
        $this->data[JsTreeDataInterface::IS_NOT_EMPTY] = $isNotEmpty;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setJsComponentConfig(array $jsComponentConfig)
    {
        $this->data[JsTreeDataInterface::JS_COMPONENT_CONFIG] = $jsComponentConfig;
        return $this;
    }
}
