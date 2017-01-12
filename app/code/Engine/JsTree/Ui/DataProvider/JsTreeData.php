<?php
namespace Engine\JsTree\Ui\DataProvider;

use Magento\Framework\Api\AbstractSimpleObject;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class JsTreeData extends AbstractSimpleObject implements JsTreeDataInterface
{
    /**
     * {@inheritdoc}
     */
    public function getTreeId()
    {
        return $this->_get(self::TREE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function isNotEmpty()
    {
        return $this->_get(self::IS_NOT_EMPTY);
    }

    /**
     * {@inheritdoc}
     */
    public function getJsComponentConfig()
    {
        return $this->_get(self::JS_COMPONENT_CONFIG);
    }
}
