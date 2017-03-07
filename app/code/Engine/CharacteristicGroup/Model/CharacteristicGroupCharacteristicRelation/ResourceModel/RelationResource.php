<?php
namespace Engine\CharacteristicGroup\Model\CharacteristicGroupCharacteristicRelation\ResourceModel;

use Engine\CharacteristicGroup\Api\Data\RelationInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class RelationResource extends AbstractDb
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(
            'engine_characteristic_group_characteristic_relation',
            RelationInterface::RELATION_ID
        );
    }
}
