<?php
namespace Engine\CharacteristicGroup\Model\CharacteristicGroup\ResourceModel;

use Engine\CharacteristicGroup\Api\Data\CharacteristicGroupInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class CharacteristicGroupResource extends AbstractDb
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('engine_characteristic_group', CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID);
    }
}
