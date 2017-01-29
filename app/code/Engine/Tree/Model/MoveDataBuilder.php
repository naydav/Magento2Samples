<?php
namespace Engine\Tree\Model;

use Engine\Tree\Api\Data\MoveDataInterface;
use Engine\Tree\Api\MoveDataBuilderInterface;
use Magento\Framework\Api\AbstractSimpleObjectBuilder;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 * @method MoveDataInterface create()
 */
class MoveDataBuilder extends AbstractSimpleObjectBuilder implements MoveDataBuilderInterface
{
    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        $this->data[MoveDataInterface::ID] = $id;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setParentId($parentId)
    {
        $this->data[MoveDataInterface::PARENT_ID] = $parentId;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setAfterId($afterId)
    {
        $this->data[MoveDataInterface::AFTER_ID] = $afterId;
        return $this;
    }
}
