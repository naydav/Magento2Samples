<?php
namespace Engine\CharacteristicGroup\Model\CharacteristicGroup\ResourceModel;

use Engine\CharacteristicGroup\Api\Data\CharacteristicGroupInterface;
use Engine\CharacteristicGroup\Model\CharacteristicGroup\CharacteristicGroup;
use Engine\PerStoreDataSupport\Model\ResourceModel\AbstractCollection;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class CharacteristicGroupCollection extends AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(CharacteristicGroup::class, CharacteristicGroupResource::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function getInterfaceName()
    {
        return CharacteristicGroupInterface::class;
    }
}
