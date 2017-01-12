<?php
namespace Engine\Framework\Tree;

use Magento\Framework\Api\AbstractSimpleObject;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class MoveData extends AbstractSimpleObject implements MoveDataInterface
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->_get(self::ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        $this->setData(self::ID, $id);
    }

    /**
     * {@inheritdoc}
     */
    public function getParentId()
    {
        return $this->_get(self::PARENT_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setParentId($parentId)
    {
        $this->setData(self::PARENT_ID, $parentId);
    }

    /**
     * {@inheritdoc}
     */
    public function getAfterId()
    {
        return $this->_get(self::AFTER_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setAfterId($afterId)
    {
        $this->setData(self::AFTER_ID, $afterId);
    }
}
